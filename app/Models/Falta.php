<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Falta extends Model
{
    protected $fillable = [
        'alumno_id',
        'fecha',
        'justificada',
        'observaciones',
        'user_id',
        'justificante_pdf'
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
