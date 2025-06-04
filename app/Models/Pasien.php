<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pasien extends Model {
    use HasFactory;
    public function bidanMitra(): BelongsTo {
        return $this->belongsTo(BidanMitra::class);
    }
    public function tindakan(): BelongsTo {
        return $this->belongsTo(Tindakan::class);
    }
}
