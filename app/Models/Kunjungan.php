<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kunjungan extends Model {
    use HasFactory;

    public function bidanMitra() {
        return $this->belongsTo(BidanMitra::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function validasiKunjungan(): HasOne {
        return $this->hasOne(ValidasiKunjungan::class);
    }
}