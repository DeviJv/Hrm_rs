<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\MarkerIcon;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MarkerIconResource\Pages;
use App\Filament\Resources\MarkerIconResource\RelationManagers;

class MarkerIconResource extends Resource {
    protected static ?string $model = MarkerIcon::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Select::make('kategori')
                    ->options([
                        'bidan' => 'Bidan',
                        'puskesmas' => 'Puskesmas',
                        'kader' => 'Kader',
                        'posyandu' => 'Posyandu',
                        'sekolah' => 'Sekolah',
                        'universitas' => 'Universitas',
                        'boarding school' => 'Boarding School',
                    ])
                    ->searchable()
                    ->required(),
                FileUpload::make('icon_path')
                    ->label('SVG Icon')

                    ->directory('marker-icons')
                    ->acceptedFileTypes(['image/svg+xml'])
                    ->preserveFilenames()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon_path')
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

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListMarkerIcons::route('/'),
            'create' => Pages\CreateMarkerIcon::route('/create'),
            'edit' => Pages\EditMarkerIcon::route('/{record}/edit'),
        ];
    }
}