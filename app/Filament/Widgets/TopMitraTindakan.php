<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Pasien;
use App\Models\BidanMitra;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class TopMitraTindakan extends BaseWidget implements Tables\Contracts\HasTable {

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static function getHeading(): string {
        $bulan = request()->input('tableFilters.bulan.bulan') ?? now()->format('m');
        $bulanFormatted = \Illuminate\Support\Carbon::create()->month($bulan)->translatedFormat('F');
        return "Top 10 Mitra Berdasarkan Tindakan - Bulan {$bulanFormatted}";
    }
    public static function canView(): bool {
        return request()->routeIs('filament.admin.dashboard.pages.dashboard-marketing');
    }

    protected function getTableQuery(): Builder {
        $bulan = request()->input('tableFilters.bulan.bulan') ?? now()->format('m');

        return BidanMitra::query()
            ->withCount([
                'pasiens as jumlah_tindakan' => function ($query) use ($bulan) {
                    $query->whereNotNull('tindakan_id')
                        ->whereMonth('created_at', $bulan);
                },
                'pasiens as jumlah_non_tindakan' => function ($query) use ($bulan) {
                    $query->whereNull('tindakan_id')
                        ->whereMonth('created_at', $bulan);
                },
            ])
            ->orderByDesc('jumlah_tindakan')
            ->limit(10);
    }

    protected function getTableColumns(): array {
        return [
            Tables\Columns\TextColumn::make('nama')
                ->label('Nama Mitra')
                ->searchable(),

            Tables\Columns\TextColumn::make('jumlah_tindakan')
                ->label('Jumlah Tindakan')
                ->numeric()
                ->sortable(),

            Tables\Columns\TextColumn::make('jumlah_non_tindakan')
                ->label('Jumlah Non-Tindakan')
                ->numeric()
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array {
        return [
            Tables\Filters\Filter::make('bulan')
                ->form([
                    Select::make('bulan')
                        ->options($this->getBulanOptions())
                        ->default(now()->format('m'))
                        ->label('Bulan'),
                ])
                ->query(function (Builder $query, array $data) {
                    $bulan = $data['bulan'] ?? now()->format('m');

                    $query->whereHas('pasiens', function ($q) use ($bulan) {
                        $q->whereMonth('created_at', $bulan);
                    });
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['bulan'] ?? null) {
                        $namaBulan = \Carbon\Carbon::create()->month((int) $data['bulan'])->translatedFormat('F');
                        $indicators['bulan'] = 'Bulan: ' . $namaBulan;
                    }
                    return $indicators;
                }),
        ];
    }

    protected function getBulanOptions(): array {
        return collect(range(1, 12))->mapWithKeys(function ($month) {
            return [
                str_pad($month, 2, '0', STR_PAD_LEFT) => Carbon::create()->month($month)->translatedFormat('F'),
            ];
        })->toArray();
    }
}