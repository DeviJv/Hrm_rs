<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pasien;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Tindakan;
use Filament\Forms\Form;
use App\Models\BidanMitra;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PasienResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PasienResource\RelationManagers;

class PasienResource extends Resource {
    protected static ?string $model = Pasien::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make()
                    ->columns(3)
                    ->schema([
                        Forms\Components\DatePicker::make('created_at')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('usia')
                            ->label('Usia')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('kelas')
                            ->required()
                            ->options([
                                'kelas 3' => 'kelas 3',
                                'kelas 2' => 'kelas 2',
                                'kelas 1' => 'kelas 1',
                                'VIP' => 'VIP',
                                'SVIP' => 'SVIP',
                                'Suite Room' => 'Suite Room',
                            ]),
                        Forms\Components\TextInput::make('pasien_rujukan')
                            ->required(),

                        Forms\Components\TextInput::make('jenis')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'diterima' => 'diterima',
                                'ditolak' => 'ditolak',
                            ]),
                        Forms\Components\Select::make('kategori')
                            ->label('Pilih Kategori')
                            ->dehydrated(false)
                            ->live()
                            ->options([
                                'bidan' => 'Bidan',
                                'puskesmas' => 'Puskesmas',
                                'kader' => 'Kader',
                                'posyandu' => 'Posyandu',
                                'sekolah' => 'Sekolah',
                                'universitas' => 'Universitas',
                                'boarding school' => 'Boarding School',
                            ])
                            ->afterStateHydrated(function (Select $component, Get $get, Set $set, $state, $operation) {
                                if ($operation == "edit") {
                                    $id = $get('bidan_mitra_id');
                                    $bidan = BidanMitra::where('id', $id)->first();
                                    $component->state($bidan->kategori);
                                }
                            })
                            ->required(),
                        Forms\Components\Select::make('bidan_mitra_id')
                            ->label('Pilih Mitra')
                            ->live()
                            ->options(function (Get $get) {
                                $kategori = $get('kategori');
                                if (filled($kategori)) {
                                    return BidanMitra::where('kategori', $kategori)->pluck('nama', 'id');
                                } else {
                                    return BidanMitra::pluck('nama', 'id');
                                }
                                return [];
                            })
                            ->required(),
                        Forms\Components\Select::make('operasi')
                            ->label('Tindakan')
                            ->live()
                            ->required()
                            ->options([
                                'ya' => 'Ya',
                                'tidak' => 'Tidak',
                            ]),
                        Forms\Components\Select::make('tindakan_id')
                            ->label('Pilih Tindakan')
                            ->live()
                            ->options(function (Get $get) {
                                if ($get('operasi') == 'ya') {
                                    return Tindakan::pluck('nama_tindakan', 'id');
                                }
                                return [];
                            }),
                        Forms\Components\Textarea::make('keterangan'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal dibuat')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bidanMitra.nama')
                    ->label('Mitra')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tindakan.nama_tindakan')
                    ->label('Tindakan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('operasi')
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
            'index' => Pages\ListPasiens::route('/'),
            'create' => Pages\CreatePasien::route('/create'),
            'view' => Pages\ViewPasien::route('/{record}'),
            'edit' => Pages\EditPasien::route('/{record}/edit'),
        ];
    }
}