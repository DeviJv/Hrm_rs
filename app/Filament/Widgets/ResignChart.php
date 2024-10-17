<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Penjualan;
use App\Models\Resign;
use App\Models\TransaksiPayroll;
use Flowframe\Trend\Trend;
use Filament\Support\RawJs;
use Flowframe\Trend\TrendValue;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ResignChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    use HasWidgetShield;
    protected static ?string $chartId = 'ResignKaryawanChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Resign Karyawan Perbulan';

    /**
     * Sort
     */
    protected static ?int $sort = 6;


    /**
     * Widget content height
     */
    protected static ?int $contentHeight = 300;

    /**
     * Filter Form
     */
    protected function getFormSchema(): array
    {
        return [

            Radio::make('ordersChartType')
                ->default('bar')
                ->options([
                    'line' => 'Line',
                    'bar' => 'Col',
                    'area' => 'Area',
                ])
                ->inline(true)
                ->label('Type'),

            Grid::make()
                ->schema([
                    Toggle::make('ordersChartMarkers')
                        ->default(false)
                        ->label('Markers'),

                    Toggle::make('ordersChartGrid')
                        ->default(false)
                        ->label('Grid'),
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
    protected function getOptions(): array
    {
        $filters = $this->filterFormData;
        $data = Trend::model(Resign::class)
            ->dateColumn('tgl_resign')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();
        return [
            'chart' => [
                'type' => $filters['ordersChartType'],
                'height' => 250,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Resign Karyawan Per Bulan',
                    'data' => $data->map(
                        fn(TrendValue $value) => $value->aggregate
                    ),
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
            'grid' => [
                'show' => $filters['ordersChartGrid'],
            ],
            'markers' => [
                'size' => $filters['ordersChartMarkers'] ? 3 : 0,
            ],
            'tooltip' => [
                'enabled' => true,
            ],
            'stroke' => [
                'width' => $filters['ordersChartType'] === 'line' ? 4 : 0,
            ],
            'colors' => ['#f59e0b'],
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
    // protected function extraJsOptions(): ?RawJs
    // {
    //     return RawJs::make(<<<'JS'
    //     {
    //         xaxis: {
    //             labels: {
    //                 formatter: function (val, timestamp, opts) {
    //                     return val
    //                 }
    //             }
    //         },
    //         yaxis: {
    //             labels: {
    //                 formatter: function (val, index) {
    //                     return new Intl.NumberFormat("id-ID", {
    //                         style: "currency",
    //                         currency: "IDR"
    //                         }).format(val)
    //                 }
    //             }
    //         },
    //         tooltip: {
    //             x: {
    //                 formatter: function (val) {
    //                     return val 
    //                 }
    //             }
    //         }
    //     }
    // JS);
    // }
}