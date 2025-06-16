<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Kunjungan;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ListKujungan extends Component implements HasForms, HasTable {
    use InteractsWithTable;
    use InteractsWithForms;
    public int $bidan_id;
    public function table(Table $table): Table {
        return $table
            ->heading('Daftar Kunjungan')
            ->query(Kunjungan::query()
                ->selectRaw('
                    MIN(id) as id,
                    user_id,
                    bidan_mitra_id,
                    MAX(created_at) as created_at,
                    COUNT(*) as jumlah_kunjungan,
                    (
                        SELECT COUNT(*)
                        FROM kunjungans as k2
                        WHERE k2.user_id = kunjungans.user_id
                        AND EXISTS (
                            SELECT 1
                            FROM validasi_kunjungans vk
                            WHERE vk.kunjungan_id = k2.id AND vk.status = "valid"
                        )
                    ) as jumlah_valid
                ')
                ->with('bidanMitra')
                ->where('bidan_mitra_id', $this->bidan_id)
                ->groupBy('user_id'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Tanggal')
                    ->sortable(),
                TextColumn::make('bidanMitra.nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Marketing')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan'),

                TextColumn::make('jumlah_kunjungan')
                    ->label('Jumlah Kunjungan')
                    ->sortable(),
                TextColumn::make('jumlah_valid')
                    ->label('Jumlah Kunjungan (Valid)')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([

                        DatePicker::make('created_from')
                            ->label('Tanggal Mulai')
                            ->placeholder(fn($state): string => 'Dec 18, ' . now()->subYear()->format('Y'))
                            ->default(now()->startOfMonth()),
                        DatePicker::make('created_until')
                            ->label('Tanggal Akhir')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];


                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Tanggal Mulai : ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Tanggal Akhir : ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ]);
    }
    public function render() {
        return view('livewire.list-kujungans');
    }
}