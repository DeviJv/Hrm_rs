<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Karyawan;
use Filament\Forms\Form;
use App\Models\Perusahaan;
use Filament\Tables\Table;
use App\Models\SuratPaklaring;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratPaklaringResource\Pages;
use App\Filament\Resources\SuratPaklaringResource\RelationManagers;

class SuratPaklaringResource extends Resource
{
    protected static ?string $model = SuratPaklaring::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 3, 'lg' => 3])
                    ->description(new HtmlString('Note : untuk <b>ALAMAT PERUSAHAAN</b> Dan <b>STAMPLE</b> di dapat module perusahaan'))

                    ->schema([
                        TextInput::make('no_surat')
                            ->default(function () {
                                $surat_paklaring = SuratPaklaring::count();
                                return "No." . $surat_paklaring + 1 . "/RSIABS-PK/SDM/" . date('m') . "/" . date('Y');
                            })
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(32)
                            ->unique(SuratPaklaring::class, 'no_surat', ignoreRecord: true),
                        Select::make('manager')
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
                                    $set('nama_manager', $karyawan->nama);
                                    $set('jabatan_manager', $karyawan->jabatan);
                                    $set('alamat', $perusahaan->alamat);
                                }
                            }),
                        Select::make('karyawan')
                            ->required()
                            ->dehydrated(false)
                            ->live()
                            ->searchable()
                            ->preload()
                            ->options(fn() => Karyawan::where('aktif', false)->pluck('nama', 'nama'))
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (filled($state)) {
                                    $karyawan = Karyawan::where('nama', $state)->first();
                                    $perusahaan = Perusahaan::first();
                                    $set('nama_karyawan', $karyawan->nama);
                                    $set('jabatan_karyawan', $karyawan->jabatan);
                                    $set('nik_karyawan', $karyawan->nik);
                                    $set('unit_karyawan', $karyawan->nakes);
                                    $set('department_karyawan', $karyawan->department);
                                    $set('alamat_karyawan', $karyawan->alamat);
                                    $set('tgl_masuk', $karyawan->tgl_masuk);
                                }
                            }),
                        DatePicker::make('tgl_masuk')
                            ->label('Tanggal Masuk')
                            ->required(),
                        DatePicker::make('tgl_keluar')
                            ->label('Tanggal Keluar')
                            ->required(),
                        TextInput::make('nama_manager')
                            ->label('Nama manager'),
                        TextInput::make('jabatan_manager')
                            ->label('Jabatan manager'),
                        TextInput::make('alamat')
                            ->label('Alamat Kantor'),
                        TextInput::make('nama_karyawan')
                            ->label('Nama Karyawan'),
                        TextInput::make('nik_karyawan')
                            ->label('Nik Karyawan'),
                        TextInput::make('jabatan_karyawan')
                            ->label('Jabatan Karyawan'),
                        TextInput::make('unit_karyawan')
                            ->label('Unit Karyawan'),
                        TextInput::make('department_karyawan')
                            ->label('Department Karyawan'),
                        TextInput::make('alamat_karyawan')
                            ->label('Alamat Karyawan'),
                        Checkbox::make('stemple')
                            ->inline(false)
                            ->default(true)
                            ->label('Tampilkan Stample?'),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_surat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_manager')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan_manager')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat Kantor')
                    ->words(5)
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_karyawan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik_karyawan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_karyawan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department_karyawan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan_karyawan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat_karyawan')
                    ->words(5)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_keluar')
                    ->date()
                    ->searchable(),
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
                            session()->put('surat_paklaring', $data);
                            return $livewire->js('window.open(\'' . route('pdf.surat_paklaring') . '\', \'_blank\');');
                            // return redirect()->;
                        })
                        ->label('Cetak Surat Paklaring'),
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
            'index' => Pages\ListSuratPaklarings::route('/'),
            'create' => Pages\CreateSuratPaklaring::route('/create'),
            'edit' => Pages\EditSuratPaklaring::route('/{record}/edit'),
        ];
    }
}