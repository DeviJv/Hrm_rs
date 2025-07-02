<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MarkerIcon extends Model {
    //

    public function getSvgContent(): string {
        return Storage::disk('public')->get($this->icon_path);
    }
}