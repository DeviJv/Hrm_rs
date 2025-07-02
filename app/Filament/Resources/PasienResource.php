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
use App\Exports\PasienExport;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\PasienResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PasienResource\RelationManagers;

class PasienResource extends Resource {
    protected static ?string $model = Pasien::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make()
                    ->columns(5)
                    ->schema([
                        Forms\Components\DatePicker::make('created_at')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('no_rm')
                            ->label('No RM'),
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
                            ->searchable()
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
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('usia')
                            ->label('Usia')
                            ->required(),
                        Forms\Components\Select::make('kelas')
                            ->required()
                            ->options([
                                'kelas 3' => 'kelas 3',
                                'kelas 2' => 'kelas 2',
                                'kelas 1' => 'kelas 1',
                                'VIP' => 'VIP',
                                'SVIP' => 'SVIP',
                                'Isolasi' => 'Isolasi',
                                'Perina' => 'Perina',
                                'NICU' => 'NICU',
                                'ICU' => 'ICU',
                                'HCU' => 'HCU',
                            ]),
                        Forms\Components\Select::make('pasien_rujukan')
                            ->label('Tipe Kunjungan')
                            ->required()
                            ->options([
                                'Rawat Inap' => 'Rawat Inap',
                                'Rawat Jalan' => 'Rawat Jalan',
                            ]),
                        Forms\Components\Select::make('jenis')
                            ->options([
                                'BPJS' => 'BPJS',
                                'Umum' => 'Umum',
                                'Asuransi' => 'Asuransi',
                            ])
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
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'diterima' => 'diterima',
                                'ditolak' => 'ditolak',
                            ]),

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
                Tables\Columns\TextColumn::make('no_rm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('usia')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->age . ' tahun'),

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
                Tables\Columns\TextColumn::make('kelas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('bidan_mitra_id')
                    ->searchable()
                    ->label('Mitra')
                    ->multiple()
                    ->options(fn() => BidanMitra::pluck('nama', 'id')),
                SelectFilter::make('status')
                    ->options([
                        'diterima' => 'diterima',
                        'ditolak' => 'ditolak',
                    ]),
                SelectFilter::make('kelas')
                    ->searchable()
                    ->multiple()
                    ->options([
                        'kelas 3' => 'kelas 3',
                        'kelas 2' => 'kelas 2',
                        'kelas 1' => 'kelas 1',
                        'VIP' => 'VIP',
                        'SVIP' => 'SVIP',
                        'Isolasi' => 'Isolasi',
                        'Perina' => 'Perina',
                        'NICU' => 'NICU',
                        'ICU' => 'ICU',
                        'HCU' => 'HCU',
                    ]),
                SelectFilter::make('jenis')
                    ->searchable()
                    ->multiple()
                    ->options([
                        'BPJS' => 'BPJS',
                        'Umum' => 'Umum',
                        'Asuransi' => 'Asuransi',
                    ]),
                SelectFilter::make('tindakan_id')
                    ->label('Tindakan')
                    ->searchable()
                    ->multiple()
                    ->options(fn() => Tindakan::pluck('nama_tindakan', 'id')),
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
                        ->action(fn(Collection $records) => (new PasienExport($records))->download('Pasien-' . date('d-m-y H i s') . '.xlsx'))
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