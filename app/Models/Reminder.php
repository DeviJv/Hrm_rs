<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reminder extends Model
{
    use HasFactory;


    protected $casts = [
        'sudah' => 'boolean',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
    public function remindable(): MorphTo
    {
        return $this->morphTo();
    }
}
