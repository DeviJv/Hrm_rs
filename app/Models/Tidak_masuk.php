<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tidak_masuk extends Model
{
    use HasFactory;
    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date:Y-m-d',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
    public function approved(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
    public function decline(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decline_by', 'id');
    }

    public function backup(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'backup_karyawan', 'id');
    }
}