<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\KewajibanKaryawan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KewajibanKaryawanResource\Pages;
use App\Filament\Resources\KewajibanKaryawanResource\RelationManagers;

class KewajibanKaryawanResource extends Resource
{
    protected static ?string $model = KewajibanKaryawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 4, 'lg' => 4])
                    ->schema([
                        Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->unique(KewajibanKaryawan::class, 'karyawan_id', ignoreRecord: true)
                            ->required(),
                        TextInput::make('bpjs_kesehatan')
                            ->label('BPJS Kesehatan')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('ketenagakerjaan')
                            ->label('BPJS Ketenagakerjaan')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('pajak')
                            ->prefix('% '),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama')

                    ->sortable(),
                Tables\Columns\TextColumn::make('bpjs_kesehatan')
                    ->default('-')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('ketenagakerjaan')
                    ->default('-'),
                Tables\Columns\TextColumn::make('pajak')
                    ->suffix('%'),
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
            'index' => Pages\ListKewajibanKaryawans::route('/'),
            'create' => Pages\CreateKewajibanKaryawan::route('/create'),
            'edit' => Pages\EditKewajibanKaryawan::route('/{record}/edit'),
        ];
    }
}
