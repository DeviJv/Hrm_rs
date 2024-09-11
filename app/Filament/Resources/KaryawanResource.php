<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Karyawan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KaryawanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KaryawanResource\RelationManagers;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->label('Form Karyawan')
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                        'lg' => 4,
                        'xl' => 6,
                        '2xl' => 8,
                    ])
                    ->schema([
                        TextInput::make('nik')
                            ->maxLength(255),
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('jk')
                            ->maxLength(255),
                        TextInput::make('nakes')
                            ->maxLength(255),
                        TextInput::make('department')
                            ->maxLength(255),
                        TextInput::make('jabatan')
                            ->maxLength(255),
                        DatePicker::make('tgl_masuk'),
                        DatePicker::make('tgl_lahir'),
                        TextInput::make('status')
                            ->maxLength(255)
                            ->default('kontrak'),
                        TextInput::make('nik_ktp')
                            ->maxLength(255),
                        TextInput::make('pendidikan')
                            ->maxLength(255),
                        TextInput::make('universitas')
                            ->maxLength(255),
                        TextInput::make('no_ijazah')
                            ->maxLength(255),
                        TextInput::make('str')
                            ->maxLength(255),
                        TextInput::make('masa_berlaku')
                            ->maxLength(255),
                        TextInput::make('sip')
                            ->maxLength(255),
                        TextInput::make('no_tlp')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Textarea::make('alamat')
                            ->columnSpanFull(),
                        TextInput::make('bank')
                            ->maxLength(255),
                        TextInput::make('no_rekening')
                            ->maxLength(255),
                        TextInput::make('nip')
                            ->maxLength(255),
                        TextInput::make('no_sk')
                            ->maxLength(255),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nakes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_masuk')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_lahir')

                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik_ktp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pendidikan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('universitas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_ijazah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('str')
                    ->searchable(),
                Tables\Columns\TextColumn::make('masa_berlaku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_tlp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('aktif')
                    ->boolean(),
                // Tables\Columns\TextColumn::make('bank')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('no_rekening')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('nip')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('no_sk')
                //     ->searchable(),
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
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}