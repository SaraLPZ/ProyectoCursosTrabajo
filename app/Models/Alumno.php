<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $fillable = [
        'nombre',
        'apellidos',
        'dni',
        'f_nacimiento',
        'email',
        'telefono',
        'codigo_postal',
        'curso_id',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function faltas()
    {
        return $this->hasMany(Falta::class);
    }
}
