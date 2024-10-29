<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kontrak;
use Filament\Forms\Get;
use App\Models\Reminder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\KontrakResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KontrakResource\RelationManagers;

class KontrakResource extends Resource
{
    protected static ?string $model = Kontrak::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 4, 'lg' => 4])
                    ->schema([
                        Forms\Components\Select::make('karyawan_id')
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('tgl_mulai')
                            ->label('Tanggal Mulai')
                            ->live()
                            ->required(function (Get $get) {
                                if ($get('seumur_hidup')) {
                                    return false;
                                } else {
                                    return true;
                                }
                            }),
                        Forms\Components\DatePicker::make('tgl_akhir')
                            ->label('Tanggal Akhir')
                            ->live()
                            ->required(function (Get $get) {
                                if ($get('seumur_hidup')) {
                                    return false;
                                } else {
                                    return true;
                                }
                            }),
                        Forms\Components\Checkbox::make('seumur_hidup')
                            ->label('Karyawan Tetap?')
                            ->inline(false)
                            ->live()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
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
                Tables\Columns\IconColumn::make('seumur_hidup')
                    ->label('Karyawan Tetap')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                $delete_reminder = Reminder::where('remindable_type', Kontrak::class)->where('remindable_id', $record->id)->delete();
                            }
                        }),
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
            'index' => Pages\ListKontraks::route('/'),
            'create' => Pages\CreateKontrak::route('/create'),
            'edit' => Pages\EditKontrak::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('karyawan')->latest();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['karyawan.nama'];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Tangga Mulai' => date('d F, Y', strtotime($record->tgl_mulai)),
            'Tangga Akhir' => date('d F, Y', strtotime($record->tgl_akhir)),
        ];
    }
    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->karyawan->nama;
    }
}
