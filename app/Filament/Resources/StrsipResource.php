<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Strsip;
use App\Models\Karyawan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Exports\StrsipExporter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\StrsipResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StrsipResource\RelationManagers;

class StrsipResource extends Resource
{
    protected static ?string $model = Strsip::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $pluralModelLabel = 'STR & SIP';

    public static function form(Form $form): Form
    {
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
                    ])
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('karyawan.nama')
                    ->label('Nama')
                    ->sortable()
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

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->color('info')
                        ->exporter(StrsipExporter::class),
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
            'index' => Pages\ListStrsips::route('/'),
            'create' => Pages\CreateStrsip::route('/create'),
            'edit' => Pages\EditStrsip::route('/{record}/edit'),
        ];
    }
}
