<?php

namespace App\Http\Controllers;
use Inertia\Inertia;

use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index()
    {
        return Inertia::render('Cursos',[
            'cursos' => Curso::orderBy('id','desc')->get(),
        ]);
    }
    public function show(Curso $curso)
    {
        return Inertia::render('CursoDetalle', [
        'curso' => $curso,
    ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_curso' => 'required|string',
            'profesor' => 'required|string',
            'email' => 'required|string',
            'aula' => 'required|string',
            'temario' => 'required|string',
            'turno' => 'required|in:manana,tarde',
            'dia_comienzo' => 'required|date',
            'dia_final' => 'required|date',
            'tecnico' => 'required|string',
        ]);

        Curso::create($data);

        return redirect()->back()->with('success','Curso creado correctamente');
    }
    //Editar registro
    public function update(Request $request, Curso $curso)
     {
        $data = $request->validate([
            'nombre_curso' => 'required|string',
            'profesor' => 'required|string',
            'email' => 'required|string',
            'aula' => 'required|string',
            'temario' => 'required|string',
            'turno' => 'required|in:manana,tarde',
            'dia_comienzo' => 'required|date',
            'dia_final' => 'required|date',
            'tecnico' => 'required|string',
        ]);

        $curso->update($data);

        return back()->with('success', 'Curso actualizado correctamente');
     }
    //Delete
    public function destroy(Curso $curso)
    {
        $curso->delete();//aunque salga en rojo en vsc es problema del propio visual, funciona

        return redirect()->back()->with('success', 'Curso eliminado');
    }
    
}