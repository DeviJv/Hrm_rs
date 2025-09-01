<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use App\Filament\Widgets\BarChart;
use App\Filament\Widgets\CutiChart;
use App\Filament\Widgets\CutiWidget;
use App\Filament\Widgets\LemburChart;
use App\Filament\Widgets\ResignChart;
use App\Filament\Widgets\LatestStrsip;
use Filament\Forms\Components\Section;
use App\Filament\Widgets\LatestKontrak;
use Filament\Forms\Components\DatePicker;
use App\Filament\Clusters\DashboardCluster;
use Filament\Pages\Dashboard as BaseDashboard;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard {
    use HasFiltersForm;

    // protected static ?string $cluster = DashboardCluster::class;
    // protected static string $routePath = 'dashboard-hrm';
    // protected static ?string $title = 'Dashboard HRM';
    // protected static ?string $navigationLabel = 'HRM';

    // public function getFooterWidgets(): array {
    //     return [
    //         BarChart::class,
    //         CutiChart::class,
    //         CutiWidget::class,
    //         LatestStrsip::class,
    //         LatestKontrak::class,
    //         LemburChart::class,
    //         ResignChart::class,
    //     ];
    // }

    public function filtersForm(Form $form): Form {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Tanggal Mulai'),
                        DatePicker::make('endDate')
                            ->label('Tanggal Akhir'),

                    ])
                    ->columns(2),
            ]);
    }
}