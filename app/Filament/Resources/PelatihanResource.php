<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pelatihan;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\PelatihanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PelatihanResource\RelationManagers;

class PelatihanResource extends Resource {
    protected static ?string $model = Pelatihan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationGroup = 'Diklat';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make()
                    ->columns(4)
                    ->schema([
                        Forms\Components\DatePicker::make('tgl_mulai')
                            ->required(),
                        Forms\Components\DatePicker::make('tgl_akhir')
                            ->required(),
                        Forms\Components\TextInput::make('nama_pelatihan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('narasumber')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('peserta')
                            ->required()
                            ->options(fn() => User::pluck('name', 'name'))
                            ->searchable()
                            ->multiple(),
                        Forms\Components\TextInput::make('jumlah_jam')
                            ->required()
                            ->numeric('numeric'),
                    ]),
                Section::make('Upload Document')
                    ->columns(4)
                    ->schema([
                        FileUpload::make('foto')
                            ->directory('logo')
                            ->image()
                            ->imageEditor(),
                        FileUpload::make('undangan')
                            ->directory('logo'),
                        FileUpload::make('materi')
                            ->directory('logo'),
                        FileUpload::make('absensi')
                            ->directory('logo'),
                    ]),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull()
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_akhir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pelatihan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('narasumber')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_jam')
                    ->numeric()
                    ->summarize(Sum::make()->label('Total Jumlah Jam'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('peserta')
                    ->searchable()
                    ->listWithLineBreaks()
                    ->bulleted(),
                Tables\Columns\TextColumn::make('foto')
                    ->view('filament.tables.columns.document')
                    ->searchable(),
                Tables\Columns\TextColumn::make('undangan')
                    ->view('filament.tables.columns.document')
                    ->searchable(),
                Tables\Columns\TextColumn::make('materi')
                    ->view('filament.tables.columns.document')
                    ->searchable(),
                Tables\Columns\TextColumn::make('absensi')
                    ->view('filament.tables.columns.document')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->html(),

            ])
            ->filters([

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Select::make('peserta')
                            ->label('Peserta')
                            ->preload()
                            ->searchable()
                            ->multiple()
                            ->options(fn() => User::pluck('name', 'name')),
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
                                $data['peserta'] ?? null,
                                fn(Builder $query, $peserta): Builder =>
                                $query->where(function ($q) use ($peserta) {
                                    foreach ($peserta as $userId) {
                                        $q->orWhereJsonContains('peserta', $userId);
                                    }
                                }),
                            )
                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('tgl_mulai', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('tgl_mulai', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['peserta'] ?? null) {
                            $indicators['peserta'] = 'Peserta : ' . implode(', ', $data['peserta']);
                        }
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Tanggal Mulai : ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Tanggal Akhir : ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPelatihans::route('/'),
            'create' => Pages\CreatePelatihan::route('/create'),
            'view' => Pages\ViewPelatihan::route('/{record}'),
            'edit' => Pages\EditPelatihan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder {
        if (!auth()->user()->hasRole('diklat')) {
            return parent::getEloquentQuery()->whereJsonContains('peserta', auth()->user()->name);
        }

        return parent::getEloquentQuery();
    }
}