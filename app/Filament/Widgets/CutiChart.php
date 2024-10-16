<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use App\Models\Tidak_masuk;
use Flowframe\Trend\TrendValue;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CutiChart extends ApexChartWidget
{
    use HasWidgetShield;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'cutiChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Cuti Chart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */

    protected static ?int $sort = 5;


    protected function getOptions(): array
    {
        $filters = $this;

        $count_tidakmasuk = Trend::query(Tidak_masuk::where('keterangan', 'cuti'))
            ->dateColumn('tgl_mulai')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();
        if (auth()->user()->hasRole('karyawan')) {
            $count_tidakmasuk = Trend::query(Tidak_masuk::where('keterangan', 'cuti')->where('karyawan_id', auth()->user()->karyawan_id))
                ->dateColumn('tgl_mulai')
                ->between(
                    start: now()->startOfYear(),
                    end: now()->endOfYear(),
                )
                ->perMonth()
                ->count();
        }
        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Cuti Chart',
                    'data' => $count_tidakmasuk->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'xaxis' => [
                'categories' => $count_tidakmasuk->map(fn(TrendValue $value) => date('F Y', strtotime($value->date))),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
        ];
    }
}
