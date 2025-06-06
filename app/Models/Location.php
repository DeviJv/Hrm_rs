<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    /**
     * REPLACE THE FOLLOWING ARRAYS IN YOUR Location MODEL
     *
     * Replace your existing $fillable and/or $guarded and/or $appends arrays with these - we already merged
     * any existing attributes from your model, and only included the one(s) that need changing.
     */


    protected $fillable = [
        'nama',
    ];

    protected $appends = [
        'nama',
    ];

    /**
     * ADD THE FOLLOWING METHODS TO YOUR Location MODEL
     *
     * The 'lat' and 'lang' attributes should exist as fields in your table schema,
     * holding standard decimal latitude and longitude coordinates.
     *
     * The 'nama' attribute should NOT exist in your table schema, rather it is a computed attribute,
     * which you will use as the field name for your Filament Google Maps form fields and table columns.
     *
     * You may of course strip all comments, if you don't feel verbose.
     */

    /**
     * Returns the 'lat' and 'lang' attributes as the computed 'nama' attribute,
     * as a standard Google Maps style Point array with 'lat' and 'lng' attributes.
     *
     * Used by the Filament Google Maps package.
     *
     * Requires the 'nama' attribute be included in this model's $fillable array.
     *
     * @return array
     */

    public function getNamaAttribute(): array
    {
        return [
            "lat" => (float)$this->lat,
            "lng" => (float)$this->lang,
        ];
    }

    /**
     * Takes a Google style Point array of 'lat' and 'lng' values and assigns them to the
     * 'lat' and 'lang' attributes on this model.
     *
     * Used by the Filament Google Maps package.
     *
     * Requires the 'nama' attribute be included in this model's $fillable array.
     *
     * @param ?array $location
     * @return void
     */
    public function setNamaAttribute(?array $location): void
    {
        if (is_array($location)) {
            $this->attributes['lat'] = $location['lat'];
            $this->attributes['lang'] = $location['lng'];
            unset($this->attributes['nama']);
        }
    }

    /**
     * Get the lat and lng attribute/field names used on this table
     *
     * Used by the Filament Google Maps package.
     *
     * @return string[]
     */
    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'lat',
            'lng' => 'lang',
        ];
    }

    /**
     * Get the name of the computed location attribute
     *
     * Used by the Filament Google Maps package.
     *
     * @return string
     */
    public static function getComputedLocation(): string
    {
        return 'nama';
    }

    public function bidanMitras()
    {
        return $this->morphedByMany(BidanMitra::class, 'locationable');
    }
}