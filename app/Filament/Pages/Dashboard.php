<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use App\Filament\Clusters\DashboardCluster;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard {
    use HasFiltersForm;

    protected static ?string $cluster = DashboardCluster::class;
    protected static string $routePath = 'dashboard-hrm';
    protected static ?string $title = 'Dashboard HRM';
    protected static ?string $navigationLabel = 'HRM';


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