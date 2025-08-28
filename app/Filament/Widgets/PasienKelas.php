<?php

namespace App\Filament\Widgets;

use App\Models\Pasien;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PasienKelas extends ApexChartWidget {
    use InteractsWithPageFilters;
    use HasWidgetShield;

    protected static ?string $chartId = 'PasienKelasChart';
    protected static ?int $sort = 4;
    protected static ?int $contentHeight = 450;
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;
    protected static ?string $pollingInterval = '10s';
    protected static ?string $loadingIndicator = 'Loading...';

    protected function getHeading(): string {
        $namaBulan = "";
        if (isset($this->filterFormData['bulan'])) {
            $bulan = (int) $this->filterFormData['bulan'] ?? now()->month;
            if ($bulan !== null) {
                $namaBulan = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');
            }
        }

        return 'Rujukan Pasien Perkelas Dibulan : ' . $namaBulan . '';
    }

    public static function canView(): bool {
        return request()->routeIs('filament.admin.dashboard.pages.dashboard-marketing');
    }

    protected function getFormSchema(): array {
        return [
            Select::make('bulan')
                ->options([
                    '1' => 'Januari',
                    '2' => 'Februari',
                    '3' => 'Maret',
                    '4' => 'April',
                    '5' => 'Mei',
                    '6' => 'Juni',
                    '7' => 'Juli',
                    '8' => 'Agustus',
                    '9' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ])
                ->default(now()->format('n')),

            TextInput::make('tahun')
                ->numeric()
                ->default(now()->year),
        ];
    }

    protected function getOptions(): array {
        $bulan = (int) $this->filterFormData['bulan'] ?? now()->month;
        $tahun = (int) $this->filterFormData['tahun'] ?? now()->year;

        $kelasList = ['Kelas 3', 'Kelas 2', 'Kelas 1', 'VIP', 'SVIP', 'Isolasi', 'Perina', 'NICU', 'ICU', 'HCU'];

        $totalData = [];
        $tindakanData = [];
        $nonTindakanData = [];

        foreach ($kelasList as $kelas) {
            $start = Carbon::create($tahun, $bulan)->startOfMonth();
            $end = Carbon::create($tahun, $bulan)->endOfMonth();

            $total = Pasien::where('kelas', $kelas)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $tindakan = Pasien::where('kelas', $kelas)
                ->whereBetween('created_at', [$start, $end])
                ->whereNotNull('tindakan_id')
                ->count();

            $nonTindakan = Pasien::where('kelas', $kelas)
                ->whereBetween('created_at', [$start, $end])
                ->whereNull('tindakan_id')
                ->count();

            $totalData[] = $total;
            $tindakanData[] = $tindakan;
            $nonTindakanData[] = $nonTindakan;
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 250,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'Semua',
                    'data' => $totalData,
                    'color' =>  '#60a5fa',
                ],
                [
                    'name' => 'Tindakan',
                    'data' => $tindakanData,
                    'color' => '#4ade80',
                ],
                [
                    'name' => 'Non-Tindakan',
                    'data' => $nonTindakanData,
                    'color' => '#f87171',
                ],
            ],
            'xaxis' => [
                'categories' => $kelasList,
                'labels' => [
                    'style' => [
                        'fontWeight' => 400,
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontWeight' => 400,
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#4ade80'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'dataLabels' => ['enabled' => false],
            'tooltip' => ['enabled' => true],
        ];
    }
}