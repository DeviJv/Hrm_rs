<?php

namespace App\Filament\Widgets;

use App\Models\Pasien;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PasienJenis extends ApexChartWidget {
    use InteractsWithPageFilters;

    protected static ?string $chartId = 'PasienJenisChart';
    protected static ?int $sort = 4;
    protected static ?int $contentHeight = 450;
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;
    protected static ?string $pollingInterval = '10s';
    protected static ?string $loadingIndicator = 'Loading...';

    protected function getHeading(): string {
        $bulan = (int) $this->filterFormData['bulan'] ?? now()->month;
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');

        return 'Rujukan Pasien Perkategori Dibulan : ' . $namaBulan . '';
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
        $jenisList = ['BPJS', 'Umum', 'Asuransi'];

        $start = Carbon::create($tahun, $bulan)->startOfMonth();
        $end = Carbon::create($tahun, $bulan)->endOfMonth();

        // Siapkan array kosong untuk setiap jenis
        $dataByJenis = [
            'BPJS' => [],
            'Umum' => [],
            'Asuransi' => [],
        ];

        foreach ($kelasList as $kelas) {
            foreach ($jenisList as $jenis) {
                $count = Pasien::where('kelas', $kelas)
                    ->where('jenis', $jenis)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();

                $dataByJenis[$jenis][] = $count;
            }
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 250,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'BPJS',
                    'data' => $dataByJenis['BPJS'],
                    'color' => '#60a5fa',
                ],
                [
                    'name' => 'Umum',
                    'data' => $dataByJenis['Umum'],
                    'color' => '#34d399',
                ],
                [
                    'name' => 'Asuransi',
                    'data' => $dataByJenis['Asuransi'],
                    'color' => '#fbbf24',
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