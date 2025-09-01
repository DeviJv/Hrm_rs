<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Tables;
use App\Models\Order;
use App\Models\Kontrak;
use Filament\Tables\Table;
use App\Filament\Resources\KontrakResource;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\PenerimaBarangResource;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;

class LatestKontrak extends BaseWidget {
    use HasWidgetShield;
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 9;

    // public static function canView(): bool {
    //     return request()->routeIs('filament.admin.dashboard.pages.dashboard');
    // }
    public function table(Table $table): Table {
        return $table
            ->query(KontrakResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->heading('Kontrak Karyawan')
            ->defaultSort('tgl_akhir', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_akhir')
                    ->label('Tanggal Akhir')
                    ->date()
                    ->sortable(),
                ProgressBar::make('bar')
                    ->label('Tindakan')

                    ->getStateUsing(function ($record) {
                        $start = Carbon::parse($record->tgl_mulai)->timestamp;
                        $end = Carbon::parse($record->tgl_akhir)->timestamp;
                        $total = $end - $start;
                        $sekarang = Carbon::now()->timestamp - $start;
                        $progress = Carbon::now()->timestamp - $start;
                        // dd(Carbon::now()->timestamp);
                        if ($start > Carbon::now()->timestamp) {
                            $total = 0;
                            $progress = 0;
                        } else {

                            $total = $total;
                            $progress = Carbon::now()->timestamp - $start;
                        }
                        if (Carbon::now()->timestamp > $end) {
                            $total = 100;
                            $progress = 100;
                        }
                        return [
                            'total' => $total,
                            'progress' => $progress,
                        ];
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('buka')
                    ->url(fn(Kontrak $record): string => KontrakResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}