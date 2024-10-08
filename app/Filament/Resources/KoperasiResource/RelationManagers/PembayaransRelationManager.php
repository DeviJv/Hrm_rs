<?php

namespace App\Filament\Resources\KoperasiResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Sum;

class PembayaransRelationManager extends RelationManager
{
    protected static string $relationship = 'pembayarans';
    protected static ?string $title = 'Pembayaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\DatePicker::make('created_at')
                    ->label('Tanggal Bayar')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('nominal')
                    ->required()
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                    ->prefix('Rp ')
                    ->numeric(),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->date(),
                TextColumn::make('nominal')
                    ->money("IDR")
                    ->summarize(Sum::make()->money("IDR")),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}