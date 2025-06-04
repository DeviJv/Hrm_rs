<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidanMitra extends Model
{
    use HasFactory;

    public function locations()
    {
        return $this->morphToMany(Location::class, 'locationable');
    }
}
