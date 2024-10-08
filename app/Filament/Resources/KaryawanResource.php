<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Karyawan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\KaryawanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KaryawanResource\RelationManagers;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->label('Form Karyawan')
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                        'lg' => 4,
                        'xl' => 5,
                        '2xl' => 6,
                    ])
                    ->schema([
                        TextInput::make('nik')
                            ->required()
                            ->unique(Karyawan::class, 'nik', ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Select::make('jk')
                            ->label('Jenis Kelamin')
                            ->options([
                                "L" => "laki-laki",
                                "P" => "Perempuan",
                            ]),
                        TextInput::make('nakes')
                            ->maxLength(255),
                        TextInput::make('department')
                            ->maxLength(255),
                        TextInput::make('jabatan')
                            ->maxLength(255),
                        DatePicker::make('tgl_masuk')
                            ->label('Tanggal Masuk'),
                        TextInput::make('tgl_lahir')
                            ->label('Tempat Dan Tanggal Lahir'),
                        Select::make('status')
                            ->default('kontrak')
                            ->options([
                                "kontrak" => "kontrak",
                                "magang" => "magang",
                                "tetap" => "tetap"
                            ]),
                        TextInput::make('nik_ktp')
                            ->maxLength(255),
                        TextInput::make('pendidikan')
                            ->maxLength(255),
                        TextInput::make('universitas')
                            ->maxLength(255),
                        TextInput::make('no_ijazah')
                            ->maxLength(255),
                        TextInput::make('str')
                            ->maxLength(255),
                        DatePicker::make('masa_berlaku')
                            ->label('Masa Berlaku STR'),
                        TextInput::make('sip')
                            ->maxLength(255),
                        DatePicker::make('masa_berlaku_sip')
                            ->label('Masa Berlaku SIP'),
                        TextInput::make('no_tlp')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Textarea::make('alamat')
                            ->columnSpanFull(),
                        TextInput::make('bank')
                            ->maxLength(255),
                        TextInput::make('no_rekening')
                            ->maxLength(255),
                        TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(255),
                        TextInput::make('no_sk')
                            ->label('NPWP')
                            ->maxLength(255),
                        Toggle::make('aktif')
                            ->inline(false)
                            ->label('Masih Bekerja?'),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                'nakes',
                'department',
            ])
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('agama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jk')
                    ->label('Jenis Kelamin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nakes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_masuk')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->label('Tempat Dan Tanggal Lahir')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik_ktp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pendidikan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('universitas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_ijazah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('str')
                    ->label('STR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('masa_berlaku')
                    ->label('Masa Berlaku STR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sip')
                    ->label('SIP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('masa_berlaku_sip')
                    ->label('Masa Berlaku SIP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_tlp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('aktif')
                    ->label('Masih Bekerja')
                    ->boolean(),
                Tables\Columns\TextColumn::make('bank')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_rekening')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('nip')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_sk')
                    ->label('npwp')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
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
                SelectFilter::make('aktif')
                    ->label('Masih Bekerja')
                    ->options([
                        0 => "Tidak Berkerja",
                        1 => "Sedang Berkerja",
                    ]),
                SelectFilter::make('department')
                    ->searchable()
                    ->options(fn() => Karyawan::groupBy('department')->pluck('department', 'department')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
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
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery();
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['nama'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->nama;
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('karyawan')) {
            return parent::getEloquentQuery()->where('karyawan_id', auth()->user()->karyawan_id);
        }
        return parent::getEloquentQuery();
    }

    public static function canCreate(): bool
    {
        if (auth()->user()->hasRole('karyawan')) {
            return false;
        }
        return true;
    }
}