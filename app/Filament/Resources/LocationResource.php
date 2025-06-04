<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Location;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use App\Filament\Resources\LocationResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use App\Filament\Resources\LocationResource\RelationManagers;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->maxLength(255),
                Geocomplete::make('location') // field name must be the computed attribute name on your model
                    ->isLocation()
                    ->geocodeOnLoad(),
                // Map::make('location')
                //     ->columnSpanFull()
                //     ->mapControls([
                //         'mapTypeControl'    => true,
                //         'scaleControl'      => true,
                //         'streetViewControl' => true,
                //         'rotateControl'     => true,
                //         'fullscreenControl' => true,
                //         'searchBoxControl'  => true, // creates geocomplete field inside map
                //         'zoomControl'       => true,
                //     ])
                //     ->height(fn() => '550px') // map height (width is controlled by Filament options)
                //     ->defaultZoom(15) // default zoom level when opening form
                //     ->autocomplete('nama')
                // field on form to use as Places geocompletion field
                // ->autocompleteReverse(true) // reverse geocode marker location to autocomplete field
                // ->reverseGeocode([
                //     'street' => '%n %S',
                //     'city' => '%L',
                //     'state' => '%A1',
                //     'zip' => '%z',
                // ]) // reverse geocode marker location to form fields, see notes below
                // ->debug() // prints reverse geocode format strings to the debug console
                // ->defaultLocation([-6.5525309, 106.7753126]) // default for new forms
                // ->draggable() // allow dragging to move marker
                // ->clickable(true) // allow clicking to move marker
                // ->geolocate() // adds a button to request device location and set map marker accordingly
                // ->geolocateLabel('Get Location') // overrides the default label for geolocate button
                // ->geolocateOnLoad(true, true), // geolocate on load, second arg 'always' (default false, only for new form))
                // ->layers([
                //     'https://googlearchive.github.io/js-v2-samples/ggeoxml/cta.kml',
                // ]) // array of KML layer URLs to add to the map
                // ->geoJson('https://fgm.test/storage/AGEBS01.geojson') // GeoJSON file, URL or JSON
                // ->geoJsonContainsField('geojson'), // field to capture GeoJSON polygon(s) which contain the map marker
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lang')
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}