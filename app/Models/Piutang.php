<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Piutang extends Model
{
    use HasFactory;
    protected function casts(): array
    {
        return [
            'created_at' => 'date:Y-m-d',
        ];
    }
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class)->where('aktif', true);
    }
    public function pembayarans(): HasMany
    {
        return $this->hasMany(PembayaranPiutang::class);
    }
}