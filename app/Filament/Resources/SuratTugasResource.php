<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Karyawan;
use Filament\Forms\Form;
use App\Models\Perusahaan;
use App\Models\SuratTugas;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratTugasResource\Pages;
use App\Filament\Resources\SuratTugasResource\RelationManagers;

class SuratTugasResource extends Resource {
    protected static ?string $model = SuratTugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'HRM';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 3, 'lg' => 3])
                    ->description(new HtmlString('Note : untuk <b>ALAMAT DIREKTUR</b> Dan <b>STAMPLE</b> di dapat module perusahaan'))

                    ->schema([
                        TextInput::make('no_surat')
                            ->default(function () {
                                $array_bln    = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
                                $surat_tugas = SuratTugas::latest()->first();
                                if ($surat_tugas != null) {
                                    $explode = explode('/', $surat_tugas->no_surat);
                                    $no = preg_replace("/[^0-9]/", '', $explode[0]);
                                } else {
                                    $no = 0;
                                }

                                return "" . $no + 1 . "/RSIABS/SDM/" . $array_bln[date('n')] . "/" . date('Y');
                            })
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(32)
                            ->unique(SuratTugas::class, 'no_surat', ignoreRecord: true),
                        Select::make('direktur')
                            ->required()
                            ->dehydrated(false)
                            ->live()
                            ->searchable()
                            ->preload()
                            ->options(fn() => Karyawan::where('aktif', true)->pluck('nama', 'nama'))
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (filled($state)) {
                                    $karyawan = Karyawan::where('nama', $state)->first();
                                    $perusahaan = Perusahaan::first();
                                    $set('nama_direktur', $karyawan->nama);
                                    $set('jabatan_direktur', $karyawan->jabatan);
                                    $set('alamat_direktur', $perusahaan->alamat);
                                }
                            }),
                        DateTimePicker::make('tgl_tugas')
                            ->label('Tanggal Dan Jam Tugas')
                            ->required(),
                        DateTimePicker::make('tgl_akhir')
                            ->label('Sampai Tanggal'),
                        TextInput::make('nama_direktur')
                            ->label('Nama direktur'),
                        TextInput::make('jabatan_direktur')
                            ->label('Jabatan direktur'),
                        TextInput::make('alamat_direktur')
                            ->label('Alamat direktur'),
                        Repeater::make('karyawans')
                            ->columnSpanFull()
                            ->schema([
                                Select::make('karyawan')
                                    ->required()
                                    ->dehydrated(false)
                                    ->live()
                                    ->searchable()
                                    ->preload()
                                    ->options(fn() => Karyawan::where('aktif', true)->pluck('nama', 'nama'))
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if (filled($state)) {
                                            $karyawan = Karyawan::where('nama', $state)->first();
                                            $perusahaan = Perusahaan::first();
                                            $set('nama_karyawan', $karyawan->nama);
                                            $set('jabatan_karyawan', $karyawan->jabatan);
                                            $set('nik_karyawan', $karyawan->nik);
                                        }
                                    }),
                                TextInput::make('nama_karyawan')
                                    ->label('Nama Karyawan'),
                                TextInput::make('nik_karyawan')
                                    ->label('Nik Karyawan'),
                                TextInput::make('jabatan_karyawan')
                                    ->label('Jabatan Karyawan'),
                            ])
                            ->columns(4),
                        TextInput::make('tempat')
                            ->label('Tempat'),
                        Checkbox::make('stemple')
                            ->inline(false)
                            ->default(true)
                            ->label('Tampilkan Stample?'),
                        Textarea::make('tugas')
                            ->columnSpanfull(),

                    ])
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tgl_tugas')
                    ->label('Tanggal Surat Tugas')
                    // ->dateTime()
                    // ->sortable(),
                    ->state(function (SuratTugas $record) {
                        if ($record->tgl_akhir != null) {
                            return date('M d, Y H:m', strtotime($record->tgl_tugas)) . " - " . date('M d, Y H:m', strtotime($record->tgl_akhir));
                        } else {
                            return date('M d, Y H:m', strtotime($record->tgl_tugas));
                        }
                    }),
                Tables\Columns\TextColumn::make('no_surat'),
                Tables\Columns\TextColumn::make('nama_direktur'),
                Tables\Columns\TextColumn::make('jabatan_direktur'),
                Tables\Columns\TextColumn::make('alamat_direktur')
                    ->words(5),
                Tables\Columns\TextColumn::make('karyawans as nama_karyawan')
                    ->label("Karyawan")
                    ->state(function (SuratTugas $record) {
                        $result = [];
                        foreach ($record->karyawans as $karyawan) {
                            $result[] = "{$karyawan['nama_karyawan']}";
                        }
                        return $result;
                    })
                    ->bulleted(),
                Tables\Columns\TextColumn::make('karyawans as nik')
                    ->label("NIK")
                    ->state(function (SuratTugas $record) {
                        $result = [];
                        foreach ($record->karyawans as $karyawan) {
                            $result[] = "{$karyawan['nik_karyawan']}";
                        }
                        return $result;
                    })
                    ->bulleted(),
                Tables\Columns\TextColumn::make('karyawans as jabatan')
                    ->label("Jabatan")
                    ->state(function (SuratTugas $record) {
                        $result = [];
                        foreach ($record->karyawans as $karyawan) {
                            $result[] = "{$karyawan['jabatan_karyawan']}";
                        }
                        return $result;
                    })
                    ->bulleted(),
                Tables\Columns\TextColumn::make('tempat')
                    ->words(4),
                Tables\Columns\TextColumn::make('tugas')
                    ->markdown(),
                Tables\Columns\IconColumn::make('stemple')
                    ->boolean(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('cetak_pdf')
                        ->color('info')
                        ->icon('heroicon-o-printer')
                        ->action(function (Collection $records, $livewire) {
                            $perusahaan = Perusahaan::first();
                            $data = [
                                'records' => $records,
                                'perusahaan' => $perusahaan,
                            ];
                            session()->put('surat_tugas', $data);
                            return $livewire->js('window.open(\'' . route('pdf.surat_tugas') . '\', \'_blank\');');
                            // return redirect()->;
                        })
                        ->label('Cetak Surat Tugas'),
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

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListSuratTugas::route('/'),
            'create' => Pages\CreateSuratTugas::route('/create'),
            'edit' => Pages\EditSuratTugas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder {
        if (auth()->user()->hasRole('karyawan')) {
            return parent::getEloquentQuery()->where('karyawan_id', auth()->user()->karyawan_id);
        }
        return parent::getEloquentQuery();
    }
}