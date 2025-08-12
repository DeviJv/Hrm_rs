<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Tindakan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MasterFeeRujukan;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MasterFeeRujukanResource\Pages;
use App\Filament\Resources\MasterFeeRujukanResource\RelationManagers;

class MasterFeeRujukanResource extends Resource {
    protected static ?string $model = MasterFeeRujukan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make()
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('tindakan')
                            ->required(),
                        Forms\Components\Select::make('kategori')
                            ->required()
                            ->options([
                                'bidan atau pkm' => 'Bidan Atau PKM',
                                'komunitas' => 'Komunitas',
                            ]),
                        Forms\Components\TextInput::make('umum')
                            ->prefix('Rp ')
                            ->required()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('asuransi')
                            ->prefix('Rp ')
                            ->required()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('bpjs')
                            ->prefix('Rp ')
                            ->required()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->numeric()
                            ->default(0),
                    ])

            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tindakan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('umum')
                    ->money('IDR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('asuransi')
                    ->money('IDR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bpjs')
                    ->money('IDR')
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
            'index' => Pages\ListMasterFeeRujukans::route('/'),
            'create' => Pages\CreateMasterFeeRujukan::route('/create'),
            'view' => Pages\ViewMasterFeeRujukan::route('/{record}'),
            'edit' => Pages\EditMasterFeeRujukan::route('/{record}/edit'),
        ];
    }
}
