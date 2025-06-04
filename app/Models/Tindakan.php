<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tindakan extends Model {
    use HasFactory;

    public function pasien(): HasMany {
        return $this->hasMany(Pasien::class);
    }
}
