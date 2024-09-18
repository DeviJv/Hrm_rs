<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Piutang;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Karyawan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\Indicator;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PiutangResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PiutangResource\RelationManagers;

class PiutangResource extends Resource
{
    protected static ?string $model = Piutang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 3, 'lg' => 3])
                    ->schema([
                        DatePicker::make('created_at')
                            ->label('Tanggal Piutang')
                            ->required(),
                        Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->required(),
                        TextInput::make('obat')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp ')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (filled($state) && filled($get('catering'))) {
                                    $set('sub_total', $state + $get('catering'));
                                }
                            }),
                        TextInput::make('catering')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp ')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (filled($state) && filled($get('obat'))) {
                                    $set('sub_total', $state + $get('obat'));
                                }
                            }),
                        TextInput::make('sub_total')
                            ->required()
                            ->live()
                            ->readOnly()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal piutang')
                    // ->date('d/m/Y')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('obat')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('catering')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'UNPAID' => 'danger',
                        'PAID' => 'success',
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\Select::make('karyawan_id')
                            ->label('Karyawan')
                            ->preload()
                            ->searchable()
                            ->relationship('karyawan', 'nama'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->searchable()
                            ->options([
                                "PAID" => "PAID",
                                "UNPAID" => "UNPAID",
                            ]),
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
                                $data['karyawan_id'] ?? null,
                                fn(Builder $query, $data): Builder => $query->where('karyawan_id', '=', $data),
                            )
                            ->when(
                                $data['status'] ?? null,
                                fn(Builder $query, $data): Builder => $query->where('status', '=', $data),
                            )
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
                        if ($data['karyawan_id'] ?? null) {
                            $cus = Karyawan::where('id', $data['karyawan_id'])->pluck('nama')->first();
                            $indicators[] = Indicator::make('Karyawan :' . $cus)
                                ->removeField('karyawan_id');
                        }
                        if ($data['status'] ?? null) {
                            $indicators['status'] = $data['status'];
                        }
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPiutangs::route('/'),
            'create' => Pages\CreatePiutang::route('/create'),
            'edit' => Pages\EditPiutang::route('/{record}/edit'),
        ];
    }
}
