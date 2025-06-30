<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\BidanMitra;
use Filament\Tables\Table;
use App\Exports\BidanMitraTest;
use Filament\Resources\Resource;
use App\Exports\BidanMitraExport;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BidanMitraResource\Pages;
use App\Filament\Resources\BidanMitraResource\Widgets\CustomBidanWidget;
use App\Filament\Resources\BidanMitraResource\Widgets\BidanMitraTableWidget;
use App\Filament\Resources\BidanMitraResource\Widgets\BidanMitra as BidanMitraWidget;

class BidanMitraResource extends Resource {
    protected static ?string $model = BidanMitra::class;
    protected static ?string $pluralModelLabel = 'Mitra RSIA Bunda Suryatni';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Marketing';


    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make()
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('Kategori')
                            ->options([
                                'bidan' => 'Bidan',
                                'puskesmas' => 'Puskesmas',
                                'kader' => 'Kader',
                                'posyandu' => 'Posyandu',
                                'sekolah' => 'Sekolah',
                                'universitas' => 'Universitas',
                                'boarding school' => 'Boarding School',
                            ])
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('kecamatan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('kelurahan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('kota')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('alamat')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telpon')
                            ->tel()
                            ->maxLength(255),
                        Select::make('status_kerja_sama')
                            ->live()
                            ->reactive()
                            ->options([
                                "sudah" => 'SUDAH',
                                "belum" => 'BELUM',
                            ]),
                        FileUpload::make('document')
                            ->directory('documents'),
                        Map::make('location')
                            ->dehydrated(false)
                            // ->relationship('locations')
                            ->debug()
                            ->columnSpanFull()

                            ->mapControls([
                                'mapTypeControl'    => true,
                                'scaleControl'      => true,
                                'streetViewControl' => true,
                                'rotateControl'     => true,
                                'fullscreenControl' => true,
                                'searchBoxControl'  => false, // creates geocomplete field inside map
                                'zoomControl'       => true,
                            ])
                            ->height(fn() => '550px') // map height (width is controlled by Filament options)
                            ->defaultZoom(15) // default zoom level when opening form
                            ->autocomplete(
                                fieldName: 'alamat',
                                placeField: 'name',
                                types: ['establishment'],
                            )
                            ->defaultLocation(function ($record) {
                                if (! $record) {
                                    return [
                                        'lat' => -6.5525309,
                                        'lng' => 106.7753126,
                                    ];
                                }
                                // Jika edit/view → ambil dari relasi (ambil lokasi terakhir misalnya)
                                return $record->locations->last()?->nama ?? [
                                    'lat' => -6.5525309,
                                    'lng' => 106.7753126,
                                ];
                            })
                            ->reactive() // optional: for search box
                            ->autocompleteReverse(true) // mengisi field autocomplete
                            ->reverseGeocode([
                                'alamat'     => '%formatted',
                                'kelurahan'  => '%A4',
                                'kecamatan'  => '%A3',
                                'kota'  => '%A2',

                            ])
                            ->live()
                            ->reactive()
                            ->draggable() // allow dragging to move marker
                            ->clickable(true) // allow clicking to move marker
                            ->geolocate() // adds a button to request device location and set map marker accordingly
                            ->geolocateLabel('Get Location') // overrides the default label for geolocate button
                            ->geolocateOnLoad(true, false)
                        // ->geojson(asset('kelurahan_bogor_colored_only.geojson'))
                        // ->markerOptions(fn(Get $get) => [
                        //     'icon' => match ($get('status')) {
                        //         'sudah' => 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        //         'belum' => 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        //         default => 'http://maps.google.com/mapfiles/ms/icons/black-dot.png',
                        //     },
                        // ])

                    ])

            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelurahan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telpon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_kerja_sama')
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Yang Dipilih')
                        ->color('info')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn(Collection $records) => (new BidanMitraExport($records))->download('Bidan-Mitra-' . date('d-m-y H i s') . '.xlsx'))
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
            'index' => Pages\ListBidanMitras::route('/'),
            'create' => Pages\CreateBidanMitra::route('/create'),
            'view' => Pages\ViewBidanMitra::route('/{record}'),
            'edit' => Pages\EditBidanMitra::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array {
        return [
            // BidanMitraWidget::class,
            // BidanMitraTableWidget::class,
            // CobaCustomWidget::class
            // CustomBidanWidget::class
        ];
    }
}