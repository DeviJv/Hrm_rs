<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Piutang;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PiutangResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PiutangResource\RelationManagers;

class PiutangResource extends Resource
{
    protected static ?string $model = Piutang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 3, 'lg' => 3])
                    ->schema([
                        DatePicker::make('created_at')
                            ->label('Tanggal Piutang')
                            ->required(),
                        Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->required(),
                        TextInput::make('obat')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp ')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (filled($state) && filled($get('catering'))) {
                                    $set('sub_total', $state + $get('catering'));
                                }
                            }),
                        TextInput::make('catering')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp ')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (filled($state) && filled($get('obat'))) {
                                    $set('sub_total', $state + $get('obat'));
                                }
                            }),
                        TextInput::make('sub_total')
                            ->required()
                            ->live()
                            ->readOnly()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal piutang')
                    // ->date('d/m/Y')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('obat')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('catering')
                    ->money('IDR'),
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
            'index' => Pages\ListPiutangs::route('/'),
            'create' => Pages\CreatePiutang::route('/create'),
            'edit' => Pages\EditPiutang::route('/{record}/edit'),
        ];
    }
}
