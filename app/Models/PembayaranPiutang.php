<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembayaranPiutang extends Model
{
    use HasFactory;

    public function piutang(): BelongsTo
    {
        return $this->belongsTo(Piutang::class);
    }
}