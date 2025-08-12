<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Kunjungan;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use App\Exports\KunjunganExport;
use Filament\Resources\Resource;
use App\Models\ValidasiKunjungan;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\KunjunganResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KunjunganResource\RelationManagers;

class KunjunganResource extends Resource {
    protected static ?string $model = Kunjungan::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make()
                    ->columns(3)
                    ->schema([
                        Forms\Components\DateTimePicker::make('created_at')
                            ->required()
                            ->label('Tanggal')
                            ->default(now()),
                        Forms\Components\Select::make('bidan_mitra_id')
                            ->label('Pilih Mitra')
                            ->required()
                            ->relationship('bidanMitra', 'nama')
                            ->searchable(),
                        Forms\Components\Select::make('user_id')
                            ->label('Pilih Marketing')
                            ->required()
                            ->relationship('user', 'name')
                            ->searchable(),
                        Forms\Components\Textarea::make('keterangan')
                            ->columnSpanFull(),
                    ])

            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Tanggal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bidanMitra.nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Marketing')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan'),
                Tables\Columns\TextColumn::make('validasiKunjungan.status')
                    ->label('Status'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->searchable()
                    ->label('Marketing')
                    ->multiple()
                    ->options(fn() => User::role('marketing')->pluck('name', 'id')),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Tanggal Mulai')
                            ->placeholder(fn($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Tanggal Akhir')
                            ->placeholder(fn($state): string => now()->format('M d, Y')),
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
                            $indicators['created_from'] = 'Tanggal Mulai ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Tanggal Akhir ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Validasi')
                    ->label(function (?Model $record) {
                        return $record->validasiKunjungan?->status ?? "Validasi";
                    })
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DateTimePicker::make('created_at')
                                    ->default(now())
                                    ->label('Tanggal')
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'valid' => 'Valid',
                                        'tidak valid' => 'Tidak Valid',
                                    ])
                                    ->required(),
                                Forms\Components\Textarea::make('keterangan')
                                    ->columnSpanFull(),
                            ])

                    ])
                    ->action(function ($data, Model $record) {
                        $valid = ValidasiKunjungan::updateOrCreate([
                            'kunjungan_id' => $record->id
                        ], [
                            'created_at' => $data['created_at'],
                            'status' => $data['status'],
                            'keterangan' => $data['keterangan'],
                        ]);

                        return Notification::make()
                            ->success()
                            ->title('Validasi Berhasil')
                            ->body("Data :<b>{$record->bidanMitra->nama}</b> dengan marketing <b>{$record->user->name}</b> berhasil di {$valid->first()->status} pada {$data['created_at']}");
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Yang Dipilih')
                        ->color('info')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn(Collection $records) => (new KunjunganExport($records))->download('Kunjungan-' . date('d-m-y H i s') . '.xlsx'))
                ]),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListKunjungans::route('/'),
            'create' => Pages\CreateKunjungan::route('/create'),
            'edit' => Pages\EditKunjungan::route('/{record}/edit'),
        ];
    }
}