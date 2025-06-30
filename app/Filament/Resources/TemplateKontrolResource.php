<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TemplateKontrol;
use Filament\Resources\Resource;
use App\Exports\TemplateKontrolExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TemplateKontrolResource\Pages;
use App\Filament\Resources\TemplateKontrolResource\RelationManagers;

class TemplateKontrolResource extends Resource {
    protected static ?string $model = TemplateKontrol::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(4)
                    ->schema([
                        Forms\Components\DatePicker::make('tgl_kontrol')
                            ->label('Tanggal Kontrol'),
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Pasien')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_rm')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                "lama" => 'Lama',
                                "baru" => 'Baru',
                            ]),
                        Forms\Components\Select::make('jk')
                            ->required()
                            ->options([
                                "pria" => 'Pria',
                                "wanita" => 'Wanita',
                            ]),
                        Forms\Components\TextInput::make('umur')
                            ->numeric(255),
                        Forms\Components\Textarea::make('alamat')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('no_hp')
                            ->numeric(),
                        Forms\Components\TextInput::make('diagnosa'),
                        Forms\Components\TextInput::make('tindakan')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('hpl')
                            ->label('HPL'),
                        Forms\Components\TextInput::make('penjamin')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('keterangan')
                            ->maxLength(255),
                    ])

            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tgl_kontrol')
                    ->label('Tanggal Kontrol')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Pasien')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_rm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('umur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('diagnosa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tindakan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hpl')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penjamin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),

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
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Yang Dipilih')
                        ->color('info')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn(Collection $records) => (new TemplateKontrolExport($records))->download('Template-kontrol-' . date('d-m-y H i s') . '.xlsx'))
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
            'index' => Pages\ListTemplateKontrols::route('/'),
            'create' => Pages\CreateTemplateKontrol::route('/create'),
            'view' => Pages\ViewTemplateKontrol::route('/{record}'),
            'edit' => Pages\EditTemplateKontrol::route('/{record}/edit'),
        ];
    }
}