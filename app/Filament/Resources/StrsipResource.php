<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Strsip;
use App\Models\Karyawan;
use App\Models\Reminder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Exports\StrsipExporter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\StrsipResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StrsipResource\RelationManagers;
use Filament\Tables\Filters\Filter;

class StrsipResource extends Resource {
    protected static ?string $model = Strsip::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $pluralModelLabel = 'STR & SIP';
    protected static ?string $navigationGroup = 'HRM';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make()
                    ->columns(['sm' => 1, 'md' => 3, 'lg' => 3])
                    ->schema([
                        Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->default(function () {
                                $roles = auth()->user()->roles;
                                if ($roles->contains('name', 'super_admin')) {;
                                } else {
                                    $karyawan = Karyawan::where('id', auth()->user()->karyawan_id)->first();
                                    return $karyawan->id;
                                }
                            })
                            ->disabled(function () {
                                $roles = auth()->user()->roles;
                                if ($roles->contains('name', 'super_admin')) {
                                    return false;
                                } else {
                                    return true;
                                }
                            })
                            ->dehydrated()
                            ->required(),
                        TextInput::make('str'),
                        DatePicker::make('masa_berlaku_str'),
                        TextInput::make('sip'),
                        DatePicker::make('masa_berlaku_sip'),
                        Checkbox::make('seumur_hidup')
                            ->label('STR Seumur Hidup?')
                            ->inline(false)
                            ->live()
                    ])
                //
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([
                TextColumn::make('karyawan.nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('karyawan.nakes')
                    ->label('Nakes')
                    ->searchable(),
                TextColumn::make('karyawan.department')
                    ->label('Department')
                    ->searchable(),
                TextColumn::make('karyawan.universitas')
                    ->label('Universitas')
                    ->searchable(),
                TextColumn::make('str')
                    ->label('STR'),
                TextColumn::make('masa_berlaku_str')
                    ->label('Masa Berlaku STR')
                    ->date(),
                TextColumn::make('sip')
                    ->label('SIP'),
                TextColumn::make('masa_berlaku_sip')
                    ->label('Masa Berlaku SIP')
                    ->date(),
                Tables\Columns\IconColumn::make('seumur_hidup')
                    ->label('STR Seumur Hidup')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->searchable()
                    ->multiple()
                    ->label('Department')
                    ->relationship('karyawan', 'department', fn(Builder $query) => $query->groupBy('department'))
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->form([
                        Select::make('nakes')
                            ->searchable()
                            ->label('Nakes')
                            ->options(fn() => Karyawan::groupBy('nakes')->pluck('nakes', 'nakes'))
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['nakes'],
                                fn(Builder $query, $date): Builder => $query->whereRelation('karyawan', 'nakes', '=', $data),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['nakes']) {
                            return null;
                        }
                        return 'Nakes :' . $data['nakes'];
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->color('info')
                        ->exporter(StrsipExporter::class),
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                $delete_reminder = Reminder::where('remindable_type', Strsip::class)->where('remindable_id', $record->id)->delete();
                            }
                        }),
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
            'index' => Pages\ListStrsips::route('/'),
            'create' => Pages\CreateStrsip::route('/create'),
            'edit' => Pages\EditStrsip::route('/{record}/edit'),
        ];
    }
}