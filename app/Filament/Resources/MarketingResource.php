<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketingResource\Pages;
use App\Filament\Resources\MarketingResource\RelationManagers;
use App\Models\Marketing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarketingResource extends Resource {
    protected static ?string $model = Marketing::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Marketing';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMarketings::route('/'),
            'create' => Pages\CreateMarketing::route('/create'),
            'view' => Pages\ViewMarketing::route('/{record}'),
            'edit' => Pages\EditMarketing::route('/{record}/edit'),
        ];
    }
}