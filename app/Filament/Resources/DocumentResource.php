<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Document;
use App\Models\Karyawan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DocumentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DocumentResource\RelationManagers;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->required()
                            ->options(fn() => Karyawan::doesntHave('document')->pluck('nama', 'id')),
                        FileUpload::make('ijazah')
                            ->directory('documents'),
                        FileUpload::make('ktp')
                            ->directory('documents'),
                        FileUpload::make('str')
                            ->directory('documents'),
                        FileUpload::make('sip')
                            ->directory('documents'),
                        FileUpload::make('npwp')
                            ->directory('documents'),
                        FileUpload::make('cv')
                            ->directory('documents'),
                        FileUpload::make('surat_lamaran')
                            ->directory('documents'),
                        FileUpload::make('pas_foto')
                            ->directory('documents'),
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
                Tables\Columns\TextColumn::make('ijazah')
                    ->view('filament.tables.columns.ijazah'),
                Tables\Columns\TextColumn::make('ktp')
                    ->view('filament.tables.columns.ktp'),
                Tables\Columns\TextColumn::make('str')
                    ->view('filament.tables.columns.str'),
                Tables\Columns\TextColumn::make('sip')
                    ->view('filament.tables.columns.sip'),
                Tables\Columns\TextColumn::make('npwp')
                    ->view('filament.tables.columns.npwp'),
                Tables\Columns\TextColumn::make('cv')
                    ->view('filament.tables.columns.cv'),
                Tables\Columns\TextColumn::make('surat_lamaran')
                    ->view('filament.tables.columns.surat_lamaran'),
                Tables\Columns\TextColumn::make('pas_foto')
                    ->view('filament.tables.columns.pas_foto'),
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}