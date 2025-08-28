<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\BidanMitra;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TargetKunjungan extends Page  implements HasForms {
    use InteractsWithForms;
    use HasWidgetShield;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Marketing';
    protected static string $view = 'filament.pages.target-kunjungan';

    public $markers = [];
    public $startDate;
    public $endDate;
    public $category;
    public $cari;

    public function mount() {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->category = null;
        $this->cari = null;
        $this->form->fill([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'category' => $this->category,
            'cari' => $this->cari,
        ]);
        $this->loadMarkers();
    }
    public function applyFilter() {
        $this->loadMarkers(); // Jalankan query baru
    }
    protected function getFormModel(): Model|string|null {
        return null;
    }
    protected function getFormSchema(): array {
        return [
            Grid::make(4)->schema([
                DatePicker::make('startDate')
                    ->label('Tanggal Mulai')
                    ->default($this->startDate)
                    ->statePath('startDate')
                    ->reactive()
                    ->afterStateUpdated(fn() => $this->loadMarkers()),

                DatePicker::make('endDate')
                    ->label('Tanggal Akhir')
                    ->default($this->endDate)
                    ->statePath('endDate')
                    ->reactive()
                    ->afterStateUpdated(fn() => $this->loadMarkers()),

                Select::make('category')
                    ->label('Kategori')
                    ->statePath('category')
                    ->options([
                        'bidan' => 'Bidan',
                        'puskesmas' => 'Puskesmas',
                        'kader' => 'Kader',
                        'klinik' => 'Klinik',
                        'posyandu' => 'Posyandu',
                        'sekolah' => 'Sekolah',
                        'universitas' => 'Universitas',
                        'boarding school' => 'Boarding School',
                    ])
                    ->placeholder('Semua Kategori')
                    ->reactive()
                    ->afterStateUpdated(fn() => $this->loadMarkers()),
                TextInput::make('cari')
                    ->label('Nama')
                    ->prefixIcon('heroicon-m-magnifying-glass')
                    ->placeholder('Masukan Nama')
                    ->default($this->cari)
                    ->statePath('cari')
                    ->reactive()
                    ->afterStateUpdated(fn() => $this->loadMarkers()),
            ]),
        ];
    }

    public function loadMarkers() {
        $query = BidanMitra::with(['locations', 'pasiens'])
            ->withCount([
                'pasiens as pasien_count',
                'kunjungans as kunjungan_valid_count' => function ($q) {
                    $q->whereDate('created_at', '>=', $this->startDate)
                        ->whereDate('created_at', '<=', $this->endDate)
                        ->whereHas('validasiKunjungan', fn($sub) => $sub->where('status', 'valid'));
                },
            ]);

        if ($this->category) {
            $query->where('kategori', $this->category);
        }
        if ($this->cari) {
            $query->where('nama', 'like', '%' . $this->cari . '%');
        }

        $mitras = $query->get();

        $this->markers = $mitras->flatMap(function ($bidan) {
            // Cek apakah bidan punya pasien dalam rentang tanggal
            $hasPatient = $bidan->pasiens()
                ->whereDate('created_at', '>=', $this->startDate)
                ->whereDate('created_at', '<=', $this->endDate)
                ->exists();

            return $bidan->locations->map(function ($loc) use ($bidan, $hasPatient) {
                return [
                    'id' => $loc->id,
                    'lat' => (float) $loc->lat,
                    'lng' => (float) $loc->lang,
                    'nama' => $bidan->nama,
                    'has_patient' => $hasPatient,
                    'has_valid_visit' => $bidan->kunjungan_valid_count > 0,
                    'info' => view('components.target-info', [
                        'bidan' => $bidan,
                    ])->render(),
                ];
            });
        })->values()->toArray();
        $this->dispatch('refreshMap', markers: $this->markers);
    }
}