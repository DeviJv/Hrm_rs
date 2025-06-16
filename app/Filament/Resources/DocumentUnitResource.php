<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DocumentUnit;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DocumentUnitResource\Pages;
use App\Filament\Resources\DocumentUnitResource\RelationManagers;

class DocumentUnitResource extends Resource {
    protected static ?string $model = DocumentUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
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
                                "SPO" => "SPO",
                                "PEDOMAN" => "PEDOMAN",
                                "PANDUNAN" => "PANDUNAN",
                                "SK" => "SK",
                            ])
                            ->required(),
                        Forms\Components\Select::make('unit')
                            ->required()
                            ->searchable()
                            ->options([
                                "IGD" => "IGD",
                                "FARMASI" => "FARMASI",
                                "KASIR" => "KASIR",
                                "FO" => "FO",
                                "LAB" => "LAB",
                                "POLIKLINIK" => "POLIKLINIK",
                                "RM" => "RM",
                                "PERINA" => "PERINA",
                                "VK" => "VK",
                                "OK" => "OK",
                                "PERAWATAN IBU" => "PERAWATAN IBU",
                                "PERAWATAN ANAK" => "PERAWATAN ANAK",
                                "IT" => "IT",
                                "IPSRS" => "IPSRS",
                                "KESEHATAN LINGKUNGAN" => "KESEHATAN LINGKUNGAN",
                                "GIZI" => "GIZI",
                                "DAPUR" => "DAPUR",
                                "LAUNDRY" => "LAUNDRY",
                                "HRD" => "HRD",
                                "LOGISTIK" => "LOGISTIK",
                                "KEUANGAN" => "KEUANGAN",
                                "ASURANSI" => "ASURANSI",
                                "BPJS" => "BPJS",
                                "SPGDT" => "SPGDT",
                                "AMBULANCE" => "AMBULANCE",
                                "SECURITY" => "SECURITY",
                                "CS" => "CS",
                                "ATEM" => "ATEM",
                                "MAINTENANCE" => "MAINTENANCE",
                                "K3RS" => "K3RS",
                            ]),
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
                Tables\Columns\TextColumn::make('unit')
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
                        "SPO" => "SPO",
                        "PEDOMAN" => "PEDOMAN",
                        "PANDUNAN" => "PANDUNAN",
                        "SK" => "SK",
                    ]),
                SelectFilter::make('unit')
                    ->searchable()
                    ->multiple()
                    ->options([
                        "IGD" => "IGD",
                        "FARMASI" => "FARMASI",
                        "KASIR" => "KASIR",
                        "FO" => "FO",
                        "LAB" => "LAB",
                        "POLIKLINIK" => "POLIKLINIK",
                        "RM" => "RM",
                        "PERINA" => "PERINA",
                        "VK" => "VK",
                        "OK" => "OK",
                        "PERAWATAN IBU" => "PERAWATAN IBU",
                        "PERAWATAN ANAK" => "PERAWATAN ANAK",
                        "IT" => "IT",
                        "IPSRS" => "IPSRS",
                        "KESEHATAN LINGKUNGAN" => "KESEHATAN LINGKUNGAN",
                        "GIZI" => "GIZI",
                        "DAPUR" => "DAPUR",
                        "LAUNDRY" => "LAUNDRY",
                        "HRD" => "HRD",
                        "LOGISTIK" => "LOGISTIK",
                        "KEUANGAN" => "KEUANGAN",
                        "ASURANSI" => "ASURANSI",
                        "BPJS" => "BPJS",
                        "SPGDT" => "SPGDT",
                        "AMBULANCE" => "AMBULANCE",
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->form([
                            TextInput::make('password')
                                ->password()
                                ->required()
                                ->rules(['current_password'])
                        ])
                        ->keyBindings(['mod+s']),
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
            'index' => Pages\ListDocumentUnits::route('/'),
            'create' => Pages\CreateDocumentUnit::route('/create'),
            'edit' => Pages\EditDocumentUnit::route('/{record}/edit'),
        ];
    }
}