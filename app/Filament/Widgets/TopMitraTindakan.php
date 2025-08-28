<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Pasien;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TopMitraTindakan extends BaseWidget {
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 9;

    public static function canView(): bool {
        return request()->routeIs('filament.admin.dashboard.pages.dashboard-marketing');
    }
    public function table(Table $table): Table {
        return $table
            ->query(function () {
                return Pasien::query()
                    ->with('bidanMitra')
                    ->select(
                        'bidan_mitra_id',
                        DB::raw('COUNT(tindakan_id) as tindakan_count'),
                        DB::raw('COUNT(*) as total_count')
                    )
                    ->whereNotNull('bidan_mitra_id')
                    ->groupBy('bidan_mitra_id')
                    ->limit(10);
            })
            ->defaultSort('tindakan_count', 'desc')
            ->heading('Top 10 Mitra Tindakan')
            ->columns([
                Tables\Columns\TextColumn::make('bidanMitra.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tindakan_count')
                    ->label('Dengan Tindakan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('non_tindakan')
                    ->label('Tanpa Tindakan')
                    ->state(fn($record) => $record->total_count - $record->tindakan_count)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
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
                            ->default(now()->format('Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['bulan'],
                                fn(Builder $query, $date): Builder => $query->whereMonth('created_at', $date),
                            )
                            ->when(
                                $data['tahun'],
                                fn(Builder $query, $date): Builder => $query->whereYear('created_at', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {

                        if ($data['bulan'] ?? null) {
                            $namaBulan = \Carbon\Carbon::create()->month((int) $data['bulan'])->translatedFormat('F');
                            $indicators['bulan'] = 'Bulan : ' . $namaBulan;
                        }
                        if ($data['tahun'] ?? null) {
                            $indicators['tahun'] = 'Tahun : ' . $data['tahun'];
                        }

                        return $indicators;
                    }),
            ]);
    }

    public function getTableRecordKey(\Illuminate\Database\Eloquent\Model $record): string {
        return $record->bidan_mitra_id;
    }
}