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

        $startOfMonth = Carbon::create($tahun, $bulan)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulan)->endOfMonth();
        $daysInMonth = $endOfMonth->day;

        $jenisList = ['BPJS', 'Umum', 'Asuransi'];

        // Siapkan array kosong untuk setiap jenis
        $dataByJenis = [
            'BPJS' => array_fill(1, $daysInMonth, 0),
            'Umum' => array_fill(1, $daysInMonth, 0),
            'Asuransi' => array_fill(1, $daysInMonth, 0),
        ];

        // Ambil data dari database dalam 1 query
        $pasienData = Pasien::selectRaw('DAY(created_at) as day, jenis, COUNT(*) as total')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('jenis', $jenisList)
            ->groupByRaw('day, jenis')
            ->get();

        foreach ($pasienData as $row) {
            $day = (int) $row->day;
            $jenis = $row->jenis;
            $dataByJenis[$jenis][$day] = (int) $row->total;
        }

        // Konversi ke array numerik (0-based) untuk ApexChart
        $series = [];
        foreach ($jenisList as $jenis) {
            $series[] = [
                'name' => $jenis,
                'data' => array_values($dataByJenis[$jenis]),
            ];
        }

        // X-axis = daftar tanggal (1 s/d jumlah hari di bulan)
        $categories = range(1, $daysInMonth);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 250,
                'toolbar' => ['show' => false],
                'stacked' => false,
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $categories,
                'title' => ['text' => 'Tanggal'],
                'labels' => [
                    'style' => [
                        'fontWeight' => 400,
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => ['text' => 'Jumlah Pasien'],
                'labels' => [
                    'style' => [
                        'fontWeight' => 400,
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'fill' => [
                'type' => 'solid',
            ],
            'dataLabels' => ['enabled' => false],
            'tooltip' => ['enabled' => true],
        ];
    }
}