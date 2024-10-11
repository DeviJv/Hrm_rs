<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PengaturanPajak;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PengaturanPajakResource\Pages;
use App\Filament\Resources\PengaturanPajakResource\RelationManagers;

class PengaturanPajakResource extends Resource
{
    protected static ?string $model = PengaturanPajak::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 4, 'lg' => 4])
                    ->schema([
                        Forms\Components\Select::make('karyawan_id')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('status')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tarif')
                            ->required()
                            ->numeric()
                            ->prefix('%')
                            ->inputMode('decimal'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('tarif')
                    ->suffix('%')
                    ->searchable(),
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
            'index' => Pages\ListPengaturanPajaks::route('/'),
            'create' => Pages\CreatePengaturanPajak::route('/create'),
            'edit' => Pages\EditPengaturanPajak::route('/{record}/edit'),
        ];
    }
}
