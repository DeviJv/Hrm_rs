<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Lembur;
use App\Models\Payroll;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Karyawan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\Indicator;
use Filament\Forms\Components\Checkbox;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Exports\LemburExporter;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\Action as TAction;
use App\Filament\Resources\LemburResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LemburResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class LemburResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Lembur::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'approve',
            'decline',
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 4, 'lg' => 4])
                    ->description(new HtmlString('Note : untuk <b>HARGA LEMBUR</b> di dapat dari penjumlahan <b>(GAJI POKO + TRANSPORT + MAKAN)</b> pada menu pengaturan payroll'))
                    ->schema([
                        Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->dehydrated(true)
                            // ->unique(Lembur::class, 'karyawan_id', ignoreRecord: true)
                            ->live()
                            ->default(function () {
                                $roles = auth()->user()->roles;
                                if ($roles->contains('name', 'super_admin')) {;
                                } else {
                                    $karyawan = Karyawan::where('id', auth()->user()->karyawan_id)->first();
                                    return $karyawan->id;
                                }
                            })
                            ->disabled(function () {
                                $roles = auth()->user()->roles;
                                if ($roles->contains('name', 'super_admin')) {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if (filled($state)) {
                                    $karyawan = Karyawan::where('id', $state)->first();
                                    $pengaturan_payroll = Payroll::where('karyawan_id', $state)->first();
                                    if (empty($pengaturan_payroll)) {
                                        $set('harga_lembur', '');
                                        return
                                            Notification::make()
                                            ->title("Ops Sepertinya <b>{$karyawan->nama}</b> Belum Punya Pengaturan Gaji Poko,dll ")
                                            ->body('Silahkan klik buat Untuk membuat pengaturan')
                                            ->warning()
                                            ->actions([
                                                Action::make('buat_pengaturan_payroll')
                                                    ->url(PengaturanPayrollResource::getUrl('create'))
                                                    ->openUrlInNewTab(),
                                            ])
                                            ->send();
                                    }
                                    $set('harga_lembur', ($pengaturan_payroll->gaji_pokok + $pengaturan_payroll->makan + $pengaturan_payroll->transport));
                                    if (filled($get('jm_mulai')) && filled($get('jm_selesai')) && filled($get('harga_lembur')) && filled($get('tgl_lembur'))) {
                                        $tgl = $get('tgl_lembur');
                                        $dari = date_create('' . $get('tgl_lembur') . '' . $state . '');
                                        $sampai = date_create('' . $get('tgl_lembur') . '' . $get('jm_selesai') . '');
                                        $hitung = date_diff($dari, $sampai);
                                        if ($hitung->h > 6) {
                                            $hitung = $hitung->h - 2;
                                        } else {
                                            $hitung = $hitung->h;
                                        }
                                        $harga_perjam = $get('harga_lembur') / 173;
                                        if (static::tanggalMerah($tgl)) {
                                            $harga_jam_pertama = $get('harga_lembur') / 173 * 2;
                                        } else {
                                            $harga_jam_pertama = $get('harga_lembur') / 173 * 1.5;
                                        }
                                        $harga_total_jam = $harga_perjam * 2 * $hitung;
                                        $set('jumlah_jam', round($hitung));
                                        $set('harga_perjam', round($harga_perjam));
                                        $set('harga_jam_pertama', round($harga_jam_pertama));
                                        $set('harga_total_jam', round($harga_total_jam));
                                        $set('total_lembur', round($harga_jam_pertama + $harga_total_jam));
                                        $set('is_holiday', false);
                                    }
                                }
                            })
                            ->required(),
                        DatePicker::make('tgl_lembur')
                            ->live()
                            ->label('Tanggal Lembur')
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (filled($state)) {
                                    $tgl = $state;
                                    $pengaturan_payroll = Payroll::where('karyawan_id', $get('karyawan_id'))->first();
                                    $set('harga_lembur', ($pengaturan_payroll->gaji_pokok + $pengaturan_payroll->makan + $pengaturan_payroll->transport));

                                    $dari = date_create('' . $get('tgl_lembur') . '' . $get('jm_mulai') . '');
                                    $sampai = date_create('' . $get('tgl_lembur') . '' . $get('jm_selesai') . '');
                                    $hitung = date_diff($dari, $sampai);
                                    if ($hitung->h > 6) {
                                        $hitung = $hitung->h - 2;
                                    } else {
                                        $hitung = $hitung->h;
                                    }
                                    $harga_perjam = $get('harga_lembur') / 173;
                                    if (static::tanggalMerah($tgl)) {
                                        $harga_jam_pertama = $get('harga_lembur') / 173 * 2;
                                    } else {
                                        $harga_jam_pertama = $get('harga_lembur') / 173 * 1.5;
                                    }
                                    $harga_total_jam = $harga_perjam * 2 * $hitung;
                                    $set('jumlah_jam', round($hitung));
                                    $set('harga_perjam', round($harga_perjam));
                                    $set('harga_jam_pertama', round($harga_jam_pertama));
                                    $set('harga_total_jam', round($harga_total_jam));
                                    $set('total_lembur', round($harga_jam_pertama + $harga_total_jam));
                                    $set('total_lembur', round($harga_jam_pertama + $harga_total_jam));
                                    $set('is_holiday', false);
                                }
                            })
                            ->required(),
                        TimePicker::make('jm_mulai')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (filled($state) && filled($get('jm_selesai')) && filled($get('tgl_lembur')) && filled($get('harga_lembur'))) {
                                    $tgl = $get('tgl_lembur');
                                    $dari = date_create('' . $get('tgl_lembur') . '' . $state . '');
                                    $sampai = date_create('' . $get('tgl_lembur') . '' . $get('jm_selesai') . '');
                                    $hitung = date_diff($dari, $sampai);
                                    if ($hitung->h > 6) {
                                        $hitung = $hitung->h - 2;
                                    } else {
                                        $hitung = $hitung->h;
                                    }
                                    $harga_perjam = $get('harga_lembur') / 173;
                                    if (static::tanggalMerah($tgl)) {
                                        $harga_jam_pertama = $get('harga_lembur') / 173 * 2;
                                    } else {
                                        $harga_jam_pertama = $get('harga_lembur') / 173 * 1.5;
                                    }
                                    $harga_total_jam = $harga_perjam * 2 * $hitung;
                                    $set('jumlah_jam', round($hitung));
                                    $set('harga_perjam', round($harga_perjam));
                                    $set('harga_jam_pertama', round($harga_jam_pertama));
                                    $set('harga_total_jam', round($harga_total_jam));
                                    $set('total_lembur', round($harga_jam_pertama + $harga_total_jam));
                                    $set('is_holiday', false);
                                }
                            })
                            ->required(),
                        TimePicker::make('jm_selesai')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (filled($state) && filled($get('jm_mulai')) && filled($get('tgl_lembur')) && filled($get('harga_lembur'))) {
                                    $tgl = $get('tgl_lembur');

                                    $dari = date_create('' . $get('tgl_lembur') . '' . $get('jm_mulai') . '');
                                    $sampai = date_create('' . $get('tgl_lembur') . '' . $state . '');
                                    $hitung = date_diff($dari, $sampai);
                                    if ($hitung->h > 6) {
                                        $hitung = $hitung->h - 2;
                                    } else {
                                        $hitung = $hitung->h;
                                    }
                                    $harga_perjam = $get('harga_lembur') / 173;
                                    if (static::tanggalMerah($tgl)) {
                                        $harga_jam_pertama = $get('harga_lembur') / 173 * 2;
                                    } else {
                                        $harga_jam_pertama = $get('harga_lembur') / 173 * 1.5;
                                    }
                                    $harga_total_jam = $harga_perjam * 2 * $hitung;
                                    $set('jumlah_jam', round($hitung));
                                    $set('harga_perjam', round($harga_perjam));
                                    $set('harga_jam_pertama', round($harga_jam_pertama));
                                    $set('harga_total_jam', round($harga_total_jam));
                                    $set('total_lembur', round($harga_jam_pertama + $harga_total_jam));
                                    $set('is_holiday', false);
                                }
                            })
                            ->required(),
                        TextInput::make('jumlah_jam')
                            ->live()
                            ->readOnly()
                            ->required(),
                        TextInput::make('harga_lembur')
                            ->required()
                            ->readOnly()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('harga_perjam')
                            ->required()
                            ->readOnly()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('harga_jam_pertama')
                            ->required()
                            ->readOnly()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('harga_total_jam')
                            ->required()
                            ->readOnly()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('total_lembur')
                            ->readOnly()
                            ->required()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        Hidden::make('user_id')
                            ->default(auth()->user()->id),
                        Checkbox::make('is_holiday')
                            ->label('Tanggal Merah?')
                            ->inline(false)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {

                                $tgl = $get('tgl_lembur');
                                $dari = date_create('' . $get('tgl_lembur') . '' . $get('jm_mulai') . '');
                                $sampai = date_create('' . $get('tgl_lembur') . '' . $get('jm_selesai') . '');
                                $hitung = date_diff($dari, $sampai);
                                if ($hitung->h > 6) {
                                    $hitung = $hitung->h - 2;
                                } else {
                                    $hitung = $hitung->h;
                                }
                                $harga_perjam = $get('harga_lembur') / 173;
                                if ($state) {
                                    $harga_jam_pertama = $get('harga_lembur') / 173 * 2;
                                } else {
                                    $harga_jam_pertama = $get('harga_lembur') / 173 * 1.5;
                                }
                                $harga_total_jam = $harga_perjam * 2 * $hitung;
                                $set('jumlah_jam', round($hitung));
                                $set('harga_perjam', round($harga_perjam));
                                $set('harga_jam_pertama', round($harga_jam_pertama));
                                $set('harga_total_jam', round($harga_total_jam));
                                $set('total_lembur', round($harga_jam_pertama + $harga_total_jam));
                            })
                            ->dehydrated(false),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat oleh')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_lembur')
                    ->label('Tanggal Lembur')
                    // ->date('d/m/Y')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jm_mulai')
                    ->label('Jam Mulai'),
                Tables\Columns\TextColumn::make('jm_selesai')
                    ->label('Tanggal Selesai'),
                Tables\Columns\TextColumn::make('jumlah_jam')
                    ->label('Jumlah Jam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_lembur')
                    ->money('IDR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_lembur')
                    ->money('IDR')
                    ->summarize(Sum::make()->label('Total')->money('IDR'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'info',
                        'approved' => 'success',
                        'decline' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\Select::make('karyawan_id')
                            ->label('Karyawan')
                            ->preload()
                            ->searchable()
                            ->relationship('karyawan', 'nama'),
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Tanggal Mulai')
                            ->placeholder(fn($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Tanggal Akhir')
                            ->placeholder(fn($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['karyawan_id'] ?? null,
                                fn(Builder $query, $data): Builder => $query->where('karyawan_id', '=', $data),
                            )

                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('tgl_lembur', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('tgl_lembur', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['karyawan_id'] ?? null) {
                            $cus = Karyawan::where('id', $data['karyawan_id'])->pluck('nama')->first();
                            $indicators[] = Indicator::make('Karyawan : ' . $cus)
                                ->removeField('karyawan_id');
                        }

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Tanggal Mulai : ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Tanggal Akhir : ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ActionGroup::make([
                    TAction::make('setujuii')
                        ->label('Approve')
                        ->color('success')
                        ->form([
                            TextInput::make('password')
                                ->password()
                                ->required()
                                ->rules(['current_password'])
                        ])
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function (Model $record) {
                            $data = $record;
                            return redirect()->route('lembur.approve', $data);
                        }),
                    TAction::make('tolakk')
                        ->label('Decline')
                        ->color('danger')
                        ->form([
                            TextInput::make('password')
                                ->password()
                                ->required()
                                ->rules(['current_password'])
                        ])
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(function (Model $record) {
                            $data = $record;
                            return redirect()->route('lembur.decline', $data);
                        }),
                ])
                    ->icon('heroicon-m-ellipsis-horizontal')
                    ->visible(fn(Lembur $record): bool => auth()->user()->can('approve_lembur', $record) && auth()->user()->can('decline_lembur', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->color('info')
                        ->exporter(LemburExporter::class),
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->form([
                            TextInput::make('password')
                                ->password()
                                ->required()
                                ->rules(['current_password'])
                        ])
                        ->keyBindings(['mod+s']),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLemburs::route('/'),
            'create' => Pages\CreateLembur::route('/create'),
            'edit' => Pages\EditLembur::route('/{record}/edit'),
        ];
    }

    public static function tanggalMerah($value): bool
    {
        $array = json_decode(file_get_contents("https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/calendar.json"), true);

        //check tanggal merah berdasarkan libur nasional
        if (isset($array[$value]) && $array[$value]["holiday"]) : return true;
            print_r($array[$value]);

        //check tanggal merah berdasarkan hari minggu
        elseif (
            date("D", strtotime($value)) === "Sun"
        ) : return true;

        //bukan tanggal merah
        else : return false;
        endif;
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('karyawan')) {
            return parent::getEloquentQuery()->where('karyawan_id', auth()->user()->karyawan_id);
        }
        return parent::getEloquentQuery();
    }
}