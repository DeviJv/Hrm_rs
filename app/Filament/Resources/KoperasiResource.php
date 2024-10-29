<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Karyawan;
use App\Models\Koperasi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\KoperasiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KoperasiResource\RelationManagers;
use App\Filament\Resources\KoperasiResource\RelationManagers\PembayaransRelationManager;

class KoperasiResource extends Resource
{
    protected static ?string $model = Koperasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 3, 'lg' => 3])
                    ->schema([
                        DatePicker::make('created_at')
                            ->label('Tanggal Koperasi')
                            ->required(),
                        Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->required(),

                        TextInput::make('tagihan')
                            ->required()
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('tenor')
                            ->required()
                            ->numeric(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal koperasi')
                    // ->date('d/m/Y')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tagihan')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('tenor'),
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->form([
                            TextInput::make('password')
                                ->password()
                                ->required()
                                ->rules(['current_password'])
                        ])
                        ->keyBindings(['mod+s']),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PembayaransRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKoperasis::route('/'),
            'create' => Pages\CreateKoperasi::route('/create'),
            'edit' => Pages\EditKoperasi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('karyawan')) {
            return parent::getEloquentQuery()->where('karyawan_id', auth()->user()->karyawan_id);
        }
        return parent::getEloquentQuery();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('karyawan')->orderBy('created_at', 'desc');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['karyawan.nama'];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Tangga Koperasi' => date('d F, Y', strtotime($record->created_at)),
            'Tagihan' => "Rp " . number_format($record->tagihan),
            'Tenor' => $record->tenor,
            'Status' => $record->status,
        ];
    }
    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->karyawan->nama;
    }
}
