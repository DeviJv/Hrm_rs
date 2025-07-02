<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class DashboardCluster extends Cluster {

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    use HasPageShield;
}