<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Payroll;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PengaturanPayroll;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PengaturanPayrollResource\Pages;
use App\Filament\Resources\PengaturanPayrollResource\RelationManagers;

class PengaturanPayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $pluralModelLabel = 'Pengaturan Payroll';

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
                            ->unique(Payroll::class, 'karyawan_id', ignoreRecord: true)
                            ->required(),
                        TextInput::make('tunjangan')
                            ->label('Jabatan')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('makan')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('transport')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('gaji_pokok')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp ')
                            ->required(),
                        TextInput::make('fungsional')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('fungsional_it')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('bpjs_kesehatan')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),
                        TextInput::make('bpjs_ketenagakerjaan')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('Rp '),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('karyawan.nama')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('gaji_pokok')
                    ->label('Gaji Poko')
                    ->money('IDR'),
                TextColumn::make('makan')
                    ->label('Uang Makan')
                    ->default(0)
                    ->money('IDR'),
                TextColumn::make('transport')
                    ->label('Transport')
                    ->default(0)
                    ->money('IDR'),
                TextColumn::make('insentif')
                    ->label('Insentif')
                    ->default(0)
                    ->money('IDR'),
                TextColumn::make('tunjangan')
                    ->toggleable()
                    ->default(0)
                    ->label('Jabatan')
                    ->money('IDR'),
                TextColumn::make('fungsional')
                    ->label('Funsional')
                    ->toggleable()
                    ->default(0)
                    ->money('IDR'),
                TextColumn::make('fungsional_it')
                    ->label('Funsional IT')
                    ->toggleable()
                    ->default(0)
                    ->money('IDR'),
                TextColumn::make('bpjs_kesehatan')
                    ->label('BPJS Kesehatan')
                    ->toggleable()
                    ->default(0)
                    ->money('IDR'),
                TextColumn::make('bpjs_ketenagakerjaan')
                    ->label('BPJS Ketenagakerjaan')
                    ->toggleable()
                    ->default(0)
                    ->money('IDR'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengaturanPayrolls::route('/'),
            'create' => Pages\CreatePengaturanPayroll::route('/create'),
            'edit' => Pages\EditPengaturanPayroll::route('/{record}/edit'),
        ];
    }
}