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
use App\Filament\Resources\StrsipResource;
use App\Models\Strsip;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;

class LatestStrsip extends BaseWidget {
    use HasWidgetShield;
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 10;

    // public static function canView(): bool {
    //     return request()->routeIs('filament.admin.dashboard.pages.dashboard');
    // }
    public function table(Table $table): Table {
        return $table
            ->query(StrsipResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->heading('Str & Sip Karyawan')
            ->defaultSort('karyawan.nama', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masa_berlaku_str')
                    ->label('Masa Berlaku STR')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('masa_berlaku_sip')
                    ->label('Masa Berlaku SIP')
                    ->date()
                    ->sortable(),
                ProgressBar::make('bar_str')
                    ->label('Progress STR')
                    ->getStateUsing(function ($record) {
                        $end = Carbon::parse($record->masa_berlaku_str)->timestamp;
                        if ($record->seumur_hidup) {
                            $total = 0;
                            $progress = 0;
                        } else {
                            $total = $end;
                            $progress =  Carbon::now()->timestamp;
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
                ProgressBar::make('bar_sip')
                    ->label('Progress SIP')
                    ->getStateUsing(function ($record) {

                        $total = Carbon::parse($record->masa_berlaku_sip)->timestamp;
                        $progress = Carbon::now()->timestamp;
                        if (Carbon::now()->timestamp > $total) {
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
                    ->url(fn(Strsip $record): string => StrsipResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}