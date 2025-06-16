<?php

namespace App\Livewire;

use App\Models\Pasien;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component;
use App\Models\Tindakan;
use App\Models\BidanMitra;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ListPasien extends Component implements HasForms, HasTable {
    use InteractsWithTable;
    use InteractsWithForms;
    public int $bidan_id;
    public function table(Table $table): Table {
        return $table
            ->heading('Daftar Pasien')
            ->query(Pasien::query()->where('bidan_mitra_id', $this->bidan_id))
            ->paginated([10, 25, 50])
            ->headerActions([
                Action::make('create')
                    ->label('Tambah')
                    ->size(ActionSize::ExtraSmall)
                    ->form([
                        Section::make()
                            ->columns(3)
                            ->schema([
                                DatePicker::make('created_at')
                                    ->label('Tanggal')
                                    ->required()
                                    ->default(now()),
                                TextInput::make('nama')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('usia')
                                    ->label('Usia')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('kelas')
                                    ->required()
                                    ->options([
                                        'kelas 3' => 'kelas 3',
                                        'kelas 2' => 'kelas 2',
                                        'kelas 1' => 'kelas 1',
                                        'VIP' => 'VIP',
                                        'SVIP' => 'SVIP',
                                        'Suite Room' => 'Suite Room',
                                    ]),
                                TextInput::make('pasien_rujukan')
                                    ->required(),

                                TextInput::make('jenis')
                                    ->required(),
                                Select::make('status')
                                    ->required()
                                    ->options([
                                        'diterima' => 'diterima',
                                        'ditolak' => 'ditolak',
                                    ]),
                                Select::make('kategori')
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
                                Select::make('bidan_mitra_id')
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
                                Select::make('operasi')
                                    ->label('Tindakan')
                                    ->live()
                                    ->required()
                                    ->options([
                                        'ya' => 'Ya',
                                        'tidak' => 'Tidak',
                                    ]),
                                Select::make('tindakan_id')
                                    ->label('Pilih Tindakan')
                                    ->live()
                                    ->options(function (Get $get) {
                                        if ($get('operasi') == 'ya') {
                                            return Tindakan::pluck('nama_tindakan', 'id');
                                        }
                                        return [];
                                    }),
                                Textarea::make('keterangan'),
                            ])
                    ])
                    ->action(function (array $data) {
                        dd($data);
                        Pasien::create($data);
                    })

            ])
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal dibuat')
                    ->date()
                    ->sortable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('bidanMitra.nama')
                    ->label('Mitra')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tindakan.nama_tindakan')
                    ->label('Tindakan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('operasi')
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
    public function render() {
        return view('livewire.list-pasien');
    }
}