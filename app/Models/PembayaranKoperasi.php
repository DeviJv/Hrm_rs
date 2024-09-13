<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranKoperasi extends Model
{
    use HasFactory;

    public function Koperasi(): BelongsTo
    {
        return $this->belongsTo(Koperasi::class);
    }
}