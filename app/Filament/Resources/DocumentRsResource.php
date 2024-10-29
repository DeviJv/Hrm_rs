<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\DocumentRs;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DocumentRsResource\Pages;
use App\Filament\Resources\DocumentRsResource\RelationManagers;

class DocumentRsResource extends Resource
{
    protected static ?string $model = DocumentRs::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type_document')
                            ->options([
                                "SK" => "SK",
                                "MOU" => "MOU",
                                "IM" => "IM",
                                "DOKUMEN PENDUKUNG" => "DOKUMEN PENDUKUNG",
                            ])
                            ->required(),
                        Forms\Components\FileUpload::make('upload_document')
                            ->directory('documents_unit')
                            ->required(),
                        Forms\Components\DatePicker::make('masa_berlaku_date')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                "BERLAKU" => "BERLAKU",
                                "TIDAK BERLAKU" => "TIDAK BERLAKU",
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type_document')
                    ->searchable(),

                Tables\Columns\TextColumn::make('upload_document')
                    ->label('Document')
                    ->view('filament.tables.columns.document_unit'),
                Tables\Columns\TextColumn::make('masa_berlaku_date'),
                Tables\Columns\TextColumn::make('status')
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
                SelectFilter::make('type_document')
                    ->searchable()
                    ->multiple()
                    ->options([
                        "SK" => "SK",
                        "MOU" => "MOU",
                        "IM" => "IM",
                        "DOKUMEN PENDUKUNG" => "DOKUMEN PENDUKUNG",
                    ]),
                SelectFilter::make('status')
                    ->searchable()

                    ->options([
                        "BERLAKU" => "BERLAKU",
                        "TIDAK BERLAKU" => "TIDAK BERLAKU",
                    ]),
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
            'index' => Pages\ListDocumentRs::route('/'),
            'create' => Pages\CreateDocumentRs::route('/create'),
            'edit' => Pages\EditDocumentRs::route('/{record}/edit'),
        ];
    }
}
