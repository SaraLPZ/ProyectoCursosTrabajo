<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Falta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class FaltaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alumnos = Alumno::orderBy('apellidos', 'asc')
            ->withCount([
                'faltas as faltas_no_justificadas_count' => function ($query) {
                    $query->where('justificada', false);
                },
            ])
            ->get();

        return Inertia::render('Asistencias', [
            'alumnos' => $alumnos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Registrar una falta
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha' => 'required|date',
            'faltas' => 'required|array',
            'faltas.*.alumno_id' => 'required|exists:alumnos,id',
            'faltas.*.falta' => 'boolean',
            'faltas.*.justificada' => 'boolean',
        ]);

        foreach ($data['faltas'] as $falta) {
            if ($falta['falta']) {
                Falta::create([
                    'alumno_id' => $falta['alumno_id'],
                    'fecha' => $data['fecha'],
                    'justificada' => $falta['justificada'] ?? false,
                    'user_id'     => auth()->id(),
                ]);
            }
        }

        return back()->with('success', 'Falta registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $faltas = Falta::with('alumno.curso', 'user')
            ->orderBy('fecha', 'desc')
            ->get();
 
        return Inertia::render('FaltasListado', [
            'faltas' => $faltas,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualizar una falta, justificarla
     */
    public function update(Request $request, Falta $id_falta)
    {
        $data = $request->validate([
            'justificada' => 'required|boolean',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $id_falta->update($data);

        return back()->with('success', 'Falta actualizada.');
    }
    /**
     * Insertar el justificante escaneado PDF
     */
    public function uploadJustificante(Request $request, Falta $falta)
    {
        $request->validate([
            'justificante_pdf' => 'required|file|mimes:pdf|max:5120',
        ]);

        // borrar anterior si existe (evita basura en storage)
        if ($falta->justificante_pdf) {
            Storage::disk('public')->delete($falta->justificante_pdf);
        }

        $path = $request->file('justificante_pdf')
            ->store('justificantes', 'public');

        $falta->update([
            'justificante_pdf' => $path,
        ]);

        return back()->with('success', 'Justificante subido correctamente.');
    }

    /**
     * Eliminar una falta.
     */
    public function destroy(Falta $id_falta)
    {
        $id_falta->delete();

        return back()->with('success', 'Falta eliminada.');
    }
}