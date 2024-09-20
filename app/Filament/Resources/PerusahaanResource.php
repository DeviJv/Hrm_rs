<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerusahaanResource\Pages;
use App\Filament\Resources\PerusahaanResource\RelationManagers;
use App\Models\Perusahaan;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PerusahaanResource extends Resource
{
    protected static ?string $model = Perusahaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Pengaturan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('email')

                                    ->maxLength(100),
                                Forms\Components\TextInput::make('alamat')
                                    ->required()
                                    ->maxLength(300),
                                Forms\Components\TextInput::make('kota')
                                    ->required(),
                                Forms\Components\TextInput::make('fax'),
                                Forms\Components\TextInput::make('kode_pos')
                                    ->numeric(),
                                Forms\Components\TextInput::make('telpon')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('telpon_2'),
                                Forms\Components\TextInput::make('website'),

                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Section::make('Logo')
                                    ->schema([
                                        FileUpload::make('logo')
                                            ->directory('logo')
                                            ->image()
                                            ->imageEditor(),
                                    ])
                                    ->collapsible(),
                                FileUpload::make('stample')
                                    ->directory('stample')
                                    ->image()
                                    ->imageEditor(),
                            ]),

                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable(),
                Tables\Columns\TextColumn::make('alamat')->limit(20),
                Tables\Columns\TextColumn::make('kota'),
                Tables\Columns\TextColumn::make('fax'),
                Tables\Columns\TextColumn::make('telpon'),
                Tables\Columns\TextColumn::make('telpon_2'),
                Tables\Columns\ImageColumn::make('logo'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListPerusahaans::route('/'),
            'create' => Pages\CreatePerusahaan::route('/create'),
            'edit' => Pages\EditPerusahaan::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
