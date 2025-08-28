<?php

namespace App\Filament\Resources;

use Filament\Forms;

use App\Models\User;
use Filament\Tables;

use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Karyawan;
use Filament\Forms\Form;

use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource {
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Pengaturan';
    // protected static ?string $navigationParentItem = 'Roles';


    // public static function getEloquentQuery(): Builder
    // {
    //     return static::getModel()::query()->where('name', 'Super'); //fungsi untuk filter sebelum boot
    // }

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->description(new HtmlString('Note ; <b>PASSWORD</b> bawaan auto terisi 12345678 bisa di isi dan di sesuaikan pas input'))
                    ->schema([
                        Forms\Components\Select::make('karyawan_id')
                            ->label('Pilih Karyawan')
                            ->searchable()
                            ->relationship('karyawan', 'nama')
                            ->preload()
                            ->live()
                            ->required(fn() => !auth()->user()->hasRole('super_admin'))
                            ->unique(User::class, 'karyawan_id', ignoreRecord: true)
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                if (filled($state)) {
                                    $karyawan = Karyawan::where('id', $state)->first();
                                    $set('name', $karyawan->nama);
                                    $set('email', $karyawan->email);
                                    $set('aktif', $karyawan->aktif);
                                }
                            }),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->translateLabel()
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->translateLabel()
                            ->required()
                            ->default('12345678')
                            ->password()
                            ->hiddenOn('edit')
                            ->minLength(8)
                            ->maxLength(20),
                        // Forms\Components\TextInput::make('phone')
                        //     ->translateLabel()
                        //     ->label('Telepon')
                        //     ->required()
                        //     ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                        //     ->tel()
                        //     ->numeric()
                        //     ->minLength(12)
                        //     ->maxLength(15),
                        // Forms\Components\Textarea::make('address')
                        //     ->required()
                        //     ->label('Alamat')
                        //     ->minLength(12)
                        //     ->maxLength(1024),
                        // Forms\Components\textinput::make('city')
                        //     ->required()
                        //     ->label('Kota')
                        //     ->minLength(3),
                        // Forms\Components\textinput::make('state')
                        //     ->label('Provinsi')
                        //     ->required()
                        //     ->minLength(3),
                        // Forms\Components\textinput::make('zip')
                        //     ->label('Kode Pos')
                        //     ->required()
                        //     ->numeric()
                        //     ->minLength(3),
                        Forms\Components\Toggle::make('aktif')
                            ->inline(false)
                            ->label('Masih Bekerja?'),
                        Forms\Components\Select::make('roles')
                            ->label('Sebagai')
                            ->preload()
                            ->required()
                            ->multiple()
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name',
                                // modifyQueryUsing: fn(Builder $query) => $query->where('name', '!=', 'super_admin'),
                            )

                    ])
                    ->columns(2)
                    ->compact(),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->label('Nama')
                    ->sortable()
                    ->weight(FontWeight::SemiBold)

                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->copyable()
                    ->translateLabel()

                    ->copyMessage('Email copied')
                    ->copyMessageDuration(1500)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->label('Sejak')
                    ->toggleable()
                    ->since(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->translateLabel()
                    ->label('Sebagai')
                    ->toggleable()
                    ->badge(),
            ])
            // ->searchPlaceholder('Search (Name,Email)')
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('Roles')->relationship('roles', 'name')
                    ->indicator("Roles")
                // ->searchable()
                // ->preload()
                // ->label("filter by roles"),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),

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
                        ->keyBindings(['mod+s'])
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array {
        return [
            // UserResource\Widgets\UserRoleOverview::class,
        ];
    }
    public static function getHeaderWidgetsColumns(): int | array {
        return 4;
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->where('name', "!=", 'super admin');
    // }
}