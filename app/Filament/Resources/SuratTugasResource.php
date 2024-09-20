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

class SuratTugasResource extends Resource
{
    protected static ?string $model = SuratTugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 3, 'lg' => 3])
                    ->description(new HtmlString('Note : untuk <b>ALAMAT DIREKTUR</b> Dan <b>STAMPLE</b> di dapat module perusahaan'))

                    ->schema([
                        TextInput::make('no_surat')
                            ->default(function () {
                                $surat_tugas = SuratTugas::count();
                                return "No." . $surat_tugas + 1 . "/SRTTGS/RSIA-BS/SDM/I/" . date('Y');
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
                        DateTimePicker::make('created_at')
                            ->label('Tanggal Dan Jam Tugas')
                            ->required(),
                        TextInput::make('nama_direktur')
                            ->label('Nama direktur'),
                        TextInput::make('jabatan_direktur')
                            ->label('Jabatan direktur'),
                        TextInput::make('alamat_direktur')
                            ->label('Alamat direktur'),
                        TextInput::make('nama_karyawan')
                            ->label('Nama Karyawan'),
                        TextInput::make('nik_karyawan')
                            ->label('Nik Karyawan'),
                        TextInput::make('jabatan_karyawan')
                            ->label('Jabatan Karyawan'),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Surat Tugas')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_surat'),
                Tables\Columns\TextColumn::make('nama_direktur'),
                Tables\Columns\TextColumn::make('jabatan_direktur'),
                Tables\Columns\TextColumn::make('alamat_direktur')
                    ->words(5),
                Tables\Columns\TextColumn::make('nama_karyawan'),
                Tables\Columns\TextColumn::make('nik_karyawan'),
                Tables\Columns\TextColumn::make('jabatan_karyawan'),

                Tables\Columns\TextColumn::make('tempat')
                    ->words(4),
                Tables\Columns\TextColumn::make('tugas')
                    ->markdown(),
                Tables\Columns\IconColumn::make('stemple')
                    ->boolean(),
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
                        ->action(function (Collection $records) {
                            $perusahaan = Perusahaan::first();
                            $data = [
                                'records' => $records,
                                'perusahaan' => $perusahaan,
                            ];
                            session()->put('surat_tugas', $data);
                            return redirect()->route('pdf.surat_tugas');
                        })
                        ->label('Cetak Slip Gaji'),
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
            'index' => Pages\ListSuratTugas::route('/'),
            'create' => Pages\CreateSuratTugas::route('/create'),
            'edit' => Pages\EditSuratTugas::route('/{record}/edit'),
        ];
    }
}
