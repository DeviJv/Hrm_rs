<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Strsip extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'seumur_hidup' => 'boolean',
        ];
    }
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function reminder(): MorphMany
    {
        return $this->morphMany(Reminder::class, 'remindable');
    }
}