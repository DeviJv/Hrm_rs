<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Karyawan;
use Filament\Forms\Form;
use App\Models\TidakMasuk;
use Filament\Tables\Table;
use App\Models\Tidak_masuk;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use App\Models\PengaturanTidakMasuk;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TidakMasukResource\Pages;
use App\Filament\Resources\TidakMasukResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class TidakMasukResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Tidak_masuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $pluralModelLabel = 'Pengajuan Cuti/Izin';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'publish',
            'approve',
            'decline',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 3, 'lg' => 3])
                    ->schema([
                        Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
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
                            ->dehydrated()
                            ->required(),
                        Select::make('keterangan')
                            ->label('Keterangan')
                            ->searchable()
                            ->options(fn() => PengaturanTidakMasuk::pluck('nama', 'nama'))
                            ->required(),
                        TextInput::make('keperluan'),
                        DatePicker::make('tgl_mulai')
                            ->label('Tanggal Mulai')
                            // ->minDate(now())
                            ->required(),
                        DatePicker::make('tgl_akhir')
                            ->label('Tanggal Masuk')
                            ->afterOrEqual('tgl_mulai')
                            ->required(),
                        Select::make('backup_karyawan')
                            ->label('Pilih Karyawan Backup')
                            ->searchable()
                            // ->relationship('backup_karyawan', 'nama')
                            ->preload()
                            ->options(fn(Get $get) => Karyawan::where('id', '!=', $get('karyawan_id'))->pluck('nama', 'id'))
                            ->required(),
                        FileUpload::make('document')
                            ->label('Lampiran')
                            ->columnSpanFull()
                            ->directory('tidak_masuk')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('karyawan.nama')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('karyawan.jabatan')
                    ->label('Jabatan')
                    ->searchable(),
                TextColumn::make('karyawan.department')
                    ->label('Departement')
                    ->searchable(),
                TextColumn::make('keterangan')
                    ->extraAttributes(['class' => 'font-semibold uppercase'])
                    ->size(TextColumn\TextColumnSize::Large),
                TextColumn::make('keperluan'),
                TextColumn::make('tgl_mulai')
                    ->date(),
                TextColumn::make('tgl_akhir')
                    ->date(),
                TextColumn::make('jumlah_hari')
                    ->summarize(Sum::make()),
                TextColumn::make('backup.nama'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'info',
                        'approved' => 'success',
                        'decline' => 'danger',
                    })
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\Select::make('karyawan_id')
                            ->label('Karyawan')
                            ->preload()
                            ->searchable()
                            ->relationship('karyawan', 'nama'),
                        Forms\Components\Select::make('keterangan')
                            ->label('Keterangan')
                            ->searchable()
                            ->options(fn() => PengaturanTidakMasuk::pluck('nama', 'nama')),
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
                                $data['keterangan'] ?? null,
                                fn(Builder $query, $data): Builder => $query->where('keterangan', '=', $data),
                            )
                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('tgl_mulai', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('tgl_mulai', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['karyawan_id'] ?? null) {
                            $cus = Karyawan::where('id', $data['karyawan_id'])->pluck('nama')->first();
                            $indicators[] = Indicator::make('Karyawan : ' . $cus)
                                ->removeField('karyawan_id');
                        }
                        if ($data['keterangan'] ?? null) {
                            $indicators['keterangan'] = 'Keterangan : ' . $data['keterangan'];
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
                    Action::make('setujuii')
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
                            return redirect()->route('cuti.approve', $data);
                        }),
                    Action::make('tolakk')
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
                            return redirect()->route('cuti.decline', $data);
                        }),
                ])
                    ->icon('heroicon-m-ellipsis-horizontal')
                    ->visible(fn(Tidak_masuk $record): bool => auth()->user()->can('approve_tidak::masuk', $record) && auth()->user()->can('decline_tidak::masuk', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTidakMasuks::route('/'),
            'create' => Pages\CreateTidakMasuk::route('/create'),
            'edit' => Pages\EditTidakMasuk::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('karyawan')) {
            return parent::getEloquentQuery()->where('karyawan_id', auth()->user()->karyawan_id);
        }
        return parent::getEloquentQuery();
    }
}
