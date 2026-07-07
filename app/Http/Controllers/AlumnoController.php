<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Curso;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class AlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Alumnos',[
            'alumnos' => Alumno::with('curso')->orderBy('id','desc')->get(),
        'cursos' => Curso::orderBy('id', 'desc')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:alumnos,dni',
            'f_nacimiento' => 'required|date',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string',
            'codigo_postal' => 'nullable|string',
            'curso_id' => 'required|exists:cursos,id',
        ]);

        Alumno::create($data);
        
        return redirect()->back()->with('success','Alumno creado correctamente');
        
    }
    public function exportPdf()
    {
        $alumnos = Alumno::orderBy('apellidos', 'asc')->get();

        $pdf = Pdf::loadView('pdf.alumnos', compact('alumnos'));

        return $pdf->download('alumnos.pdf');
    }

    public function previewPdf()
    {
        $alumnos = Alumno::orderBy('apellidos', 'asc')->get();

        $pdf = Pdf::loadView('pdf.alumnos', compact('alumnos'));

        // stream() lo sirve inline en el navegador en lugar de forzar descarga
        return $pdf->stream('alumnos.pdf');
    }
    public function exportFirmas(Curso $curso)
    {
        $alumnos = $curso->alumnos()->orderBy('apellidos')->get();

        $pdf = Pdf::loadView('pdf.firmasasist', compact('curso', 'alumnos'))
                ->setPaper('a4', 'landscape'); // landscape encaja mejor la tabla

        return $pdf->stream("firmas_{$curso->id}.pdf");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alumno $id_alumno)
    {
        $id_alumno->delete();

        return back()->with('success', 'Alumno eliminado correctamente.');
    }
}
