<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Koperasi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KoperasiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KoperasiResource\RelationManagers;

class KoperasiResource extends Resource
{
    protected static ?string $model = Koperasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 3, 'lg' => 3])
                    ->schema([
                        DatePicker::make('created_at')
                            ->label('Tanggal Koperasi')
                            ->required(),
                        Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->required(),

                        TextInput::make('tagihan')
                            ->required()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        Select::make('tenor')
                            ->options([
                                '1 Bulan' => '1 BULAN',
                                '3 Bulan' => '3 BULAN',
                                '6 Bulan' => '6 BULAN',
                                '1 Tahun' => '1 TAHUN',
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal koperasi')
                    // ->date('d/m/Y')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tagihan')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('tenor'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'UNPAID' => 'danger',
                        'PAID' => 'success',
                    }),
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
            'index' => Pages\ListKoperasis::route('/'),
            'create' => Pages\CreateKoperasi::route('/create'),
            'edit' => Pages\EditKoperasi::route('/{record}/edit'),
        ];
    }
}
