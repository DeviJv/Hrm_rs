<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Widgets\AccountWidget;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Filament\Clusters\DashboardCluster;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class DashboardMarketing extends BaseDashboard {
    use HasFiltersForm;

    protected static ?string $cluster = DashboardCluster::class;
    protected static string $routePath = 'dashboard-marketing';
    protected static ?string $title = 'Dashboard Marketing';
    protected static ?string $navigationLabel = 'Marketing';


    // public function filtersForm(Form $form): Form {
    //     return $form
    //         ->schema([
    //             Section::make()
    //                 ->schema([
    //                     TextInput::make('tahun')
    //                         ->label('Tahun')
    //                         ->default(now()->format('Y'))
    //                         ->required()
    //                         ->numeric(),
    //                 ])
    //                 ->columns(2),
    //         ]);
    // }
}