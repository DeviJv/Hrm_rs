<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateWhatsappResource\Pages;
use App\Filament\Resources\TemplateWhatsappResource\RelationManagers;
use App\Models\TemplateWhatsapp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplateWhatsappResource extends Resource {
    protected static ?string $model = TemplateWhatsapp::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Pesan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Pesan')
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
            'index' => Pages\ListTemplateWhatsapps::route('/'),
            'create' => Pages\CreateTemplateWhatsapp::route('/create'),
            'view' => Pages\ViewTemplateWhatsapp::route('/{record}'),
            'edit' => Pages\EditTemplateWhatsapp::route('/{record}/edit'),
        ];
    }
}
