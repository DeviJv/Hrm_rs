<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Resign;
use App\Models\Karyawan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\ResignResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ResignResource\RelationManagers;

class ResignResource extends Resource
{
    protected static ?string $model = Resign::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 3, 'lg' => 3])
                    ->schema([
                        Forms\Components\Select::make('karyawan_id')
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->searchable()
                            ->disabled(function (string $operation) {
                                if ($operation == "edit") {
                                    return true;
                                } else {
                                    return false;
                                }
                            })
                            ->unique(Resign::class, 'karyawan_id', ignoreRecord: true)
                            ->required(),
                        Forms\Components\DatePicker::make('tgl_resign')
                            ->label('Tanggal Resign')
                            ->required(),
                        Forms\Components\TextInput::make('keterangan')
                            ->maxLength(255),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_resign')
                    ->label('Tanggal Resign')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->databaseTransaction()
                        ->requiresConfirmation()
                        ->form([
                            TextInput::make('password')
                                ->password()
                                ->required()
                                ->rules(['current_password'])
                        ])
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                $karyawan = Karyawan::where('id', $record->karyawan_id)->update(['aktif' => true]);
                            }
                        }),
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
            'index' => Pages\ListResigns::route('/'),
            'create' => Pages\CreateResign::route('/create'),
            'edit' => Pages\EditResign::route('/{record}/edit'),
        ];
    }
}