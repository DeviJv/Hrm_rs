<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('tgl_mulai')
                            ->label('Tanggal Mulai')
                            ->maxDate(fn(Get $get) => $get('tgl_akhir') ?: now()),
                        DatePicker::make('tgl_akhir')
                            ->label('Tanggal Akhir')
                            ->minDate(fn(Get $get) => $get('tgl_mulai') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(2),
            ]);
    }
}