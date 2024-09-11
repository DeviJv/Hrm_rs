<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Karyawan extends Model
{
    use HasFactory;
    protected function casts(): array
    {
        return [
            'tgl_masuk' => 'date:Y-m-d',
        ];
    }

    public function document(): HasOne
    {
        return $this->hasOne(Document::class);
    }
    public function tidak_masuks(): HasMany
    {
        return $this->hasMany(Tidak_masuk::class, 'karyawan_id', 'id');
    }
}