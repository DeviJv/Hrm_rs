<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Strsip extends Model
{
    use HasFactory;


    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}