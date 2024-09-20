<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{

    protected function casts(): array
    {
        return [
            'stample' => 'boolean',
        ];
    }
    use HasFactory;
}
