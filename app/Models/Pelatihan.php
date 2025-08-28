<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model {
    //
    protected $casts = [
        'peserta' => 'array',
    ];
}