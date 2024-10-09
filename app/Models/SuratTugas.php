<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'stample' => 'boolean',
            'karyawans' => 'array'
        ];
    }
}