<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lembur extends Model
{
    use HasFactory;

    // protected function casts(): array
    // {
    //     return [
    //         'tgl_lembur' => 'date:d-m-Y',
    //     ];
    // }


    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approved(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
    public function decline(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decline_by', 'id');
    }
}
