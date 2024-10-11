<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kontrak extends Model
{
    use HasFactory;

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}
