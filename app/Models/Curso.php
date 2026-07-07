<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'nombre_curso',
        'profesor',
        'email',
        'aula',
        'temario',
        'turno',
        'dia_comienzo',
        'dia_final',
        'tecnico',
    ];
    public function alumnos()
{
    return $this->hasMany(Alumno::class);
}
}
