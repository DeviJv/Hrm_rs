<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\DocumentPt;
use Filament\Tables\Table;
use App\Models\Document_pt;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DocumentPtResource\Pages;
use App\Filament\Resources\DocumentPtResource\RelationManagers;

class DocumentPtResource extends Resource {
    protected static ?string $model = Document_pt::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $pluralModelLabel = 'Document PT';
    protected static ?string $navigationGroup = 'HRM';

    public static function form(Form $form): Form {
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
                                "PERIZINAN" => "PERIZINAN",
                                "DOKUMEN TEKNIS" => "DOKUMEN TEKNIS",
                                "SERTIFIKAT" => "SERTIFIKAT",
                                "DOKUMEN PENDUKUNG" => "DOKUMEN PENDUKUNG",
                                "LAPORAN" => "LAPORAN",
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

    public static function table(Table $table): Table {
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
                        "PERIZINAN" => "PERIZINAN",
                        "DOKUMEN TEKNIS" => "DOKUMEN TEKNIS",
                        "SERTIFIKAT" => "SERTIFIKAT",
                        "DOKUMEN PENDUKUNG" => "DOKUMEN PENDUKUNG",
                        "LAPORAN" => "LAPORAN",
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

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListDocumentPts::route('/'),
            'create' => Pages\CreateDocumentPt::route('/create'),
            'edit' => Pages\EditDocumentPt::route('/{record}/edit'),
        ];
    }
}