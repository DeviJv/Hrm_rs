<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Karyawan;
use Filament\Forms\Form;
use App\Models\TidakMasuk;
use Filament\Tables\Table;
use App\Models\Tidak_masuk;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TidakMasukResource\Pages;
use App\Filament\Resources\TidakMasukResource\RelationManagers;
use App\Models\PengaturanTidakMasuk;

class TidakMasukResource extends Resource
{
    protected static ?string $model = Tidak_masuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->required(),
                        Select::make('keterangan')
                            ->label('Keterangan')
                            ->searchable()
                            ->options(fn() => PengaturanTidakMasuk::pluck('nama', 'nama'))
                            ->required(),
                        TextInput::make('keperluan'),
                        DatePicker::make('tgl_mulai')
                            // ->minDate(now())
                            ->required(),
                        DatePicker::make('tgl_akhir')
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
                TextColumn::make('backup.nama'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
}