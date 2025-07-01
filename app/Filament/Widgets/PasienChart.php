<?php

namespace App\Filament\Widgets;

use App\Models\Pasien;
use App\Models\Penjualan;
use Flowframe\Trend\Trend;
use Filament\Support\RawJs;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use App\Models\TransaksiPayroll;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PasienChart extends ApexChartWidget {
    /**
     * Chart Id
     */
    use  InteractsWithPageFilters;


    protected static ?string $chartId = 'PasienChart';

    /**
     * Widget Title
     */

    /**
     * Sort
     */
    protected static ?int $sort = 4;


    /**
     * Widget content height
     */
    protected static ?int $contentHeight = 450;
    protected int | string | array $columnSpan = 'full';

    /**
     * Filter Form
     */

    protected static bool $isLazy = false; // 👈 ini wajib untuk non-lazy
    protected static ?string $pollingInterval = '10s';
    protected static ?string $loadingIndicator = 'Loading...';

    protected function getHeading(): string {
        return 'Pasien Rujukan Perbulan ';
    }
    public static function canView(): bool {
        return request()->routeIs('filament.admin.dashboard.pages.dashboard-marketing');
    }

    protected function getFormSchema(): array {
        return [
            TextInput::make('tahun')
                ->label('Tahun')
                ->default(now()->format('Y'))
                ->required()
                ->numeric(),
            Select::make('kategori')
                ->label('Kategori')
                ->options([
                    'semua' => 'Semua',
                    'bidan' => 'Bidan',
                    'puskesmas' => 'Puskesmas',
                    'kader' => 'Kader',
                    'posyandu' => 'Posyandu',
                    'sekolah' => 'Sekolah',
                    'universitas' => 'Universitas',
                    'boarding school' => 'Boarding School',
                ])
                ->default('semua')
                ->required(),
            // Radio::make('ordersChartType')
            //     ->default('bar')
            //     ->options([
            //         'line' => 'Line',
            //         'bar' => 'Col',
            //         'area' => 'Area',
            //     ])
            //     ->inline(true)
            //     ->label('Type'),

            Grid::make()
                ->schema([
                    // Toggle::make('ordersChartMarkers')
                    //     ->default(false)
                    //     ->label('Markers'),

                    // Toggle::make('ordersChartGrid')
                    //     ->default(false)
                    //     ->label('Grid'),
                ]),

            // TextInput::make('ordersChartAnnotations')
            //     ->required()
            //     ->numeric()
            //     ->default(7500)
            //     ->label('Annotations'),
        ];
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array {
        $tahun = $this->filterFormData['tahun'] ?? now()->format('Y');
        $kategori = $this->filterFormData['kategori'];
        $data = $this->getPasienTrend('diterima', $tahun, $kategori);
        $data_di_tolak = $this->getPasienTrend('ditolak', $tahun, $kategori);
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 250,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Rujukan Pasien Mitra Perbulan Yang Diterima',
                    'data' => $data->map(
                        fn(TrendValue $value) => $value->aggregate
                    ),
                    'color' => '#4ade80',
                ],
                [
                    'name' => 'Rujukan Pasien Mitra Perbulan Yang Ditolak',
                    'data' => $data_di_tolak->map(
                        fn(TrendValue $value) => $value->aggregate
                    ),
                    'color' => '#ef4444', // merah
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->format('M-Y')),
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

            'dataLabels' => [
                'enabled' => false,
            ],
            // 'grid' => [
            //     'show' => $filters['ordersChartGrid'],
            // ],
            // 'markers' => [
            //     'size' => $filters['ordersChartMarkers'] ? 3 : 0,
            // ],
            'tooltip' => [
                'enabled' => true,
            ],
            // 'stroke' => [
            //     'width' => $filters['ordersChartType'] === 'line' ? 4 : 0,
            // ],
            // 'colors' => ['#f59e0b'],
            'annotations' => [
                'yaxis' => [
                    [
                        // 'y' => $filters['ordersChartAnnotations'],
                        'borderColor' => '#4ade80',
                        'borderWidth' => 1,
                        'label' => [
                            'borderColor' => '#4ade80',
                            'style' => [
                                'color' => '#fffbeb',
                                'background' => '#4ade80',
                            ],
                            // 'text' => 'Annotation: ' . $filters['ordersChartAnnotations'],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getPasienTrend(string $status, int|string $tahun, string|null $kategori): \Illuminate\Support\Collection {
        return Trend::query(
            Pasien::query()
                ->where('status', $status)
                ->when($kategori !== 'semua' && $kategori !== null, function ($query) use ($kategori) {
                    $query->whereRelation('bidanMitra', 'kategori', $kategori);
                })
        )
            ->between(
                start: Carbon::create($tahun)->startOfYear(),
                end: Carbon::create($tahun)->endOfYear(),
            )
            ->perMonth()
            ->count();
    }
}