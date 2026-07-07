<?php

use App\Http\Controllers\AlumnoController;
use Illuminate\Support\Facades\Route;
use App\Models\Curso;
use App\Models\Alumno;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\FaltaController;
use App\Http\Controllers\UserController;


Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('/cursos', [CursoController::class, 'index'])->name('cursos');
    Route::inertia('calendario', 'Calendario', [
    'cursos' => Curso::all()
    ])->name('calendario');

    
    //Post de cursos para crear el curso
    Route::post('/cursos', [CursoController::class, 'store'])
    ->name('cursos.store');
    //Editar el registro
    Route::put('/cursos/{curso}', [CursoController::class, 'update'])
    ->name('cursos.update');
    //Eliminar curso
    Route::delete('/cursos/{curso}', [CursoController::class, 'destroy']);
    //Cursos Show Details
    Route::get('/cursos/{curso}/detail',[CursoController::class,'show'])->name('show');
    //Firmas alumnos semana curso
    Route::get('/cursos/{curso}/firmas', [AlumnoController::class, 'exportFirmas'])->name('cursos.firmas');

    //Alumnos
    Route::get('/alumnos',[AlumnoController::class,'index'])->name('alumnos');
    Route::post('/alumnos', [AlumnoController::class,'store'])->name('alumnos.store');
    Route::delete('/alumnos/{id_alumno}', [AlumnoController::class,'destroy'])->name('alumnos.destroy');
    //PDF para alumnos prueba
    Route::get('/alumnos/pdf', [AlumnoController::class, 'exportPdf'])->name('alumnos.pdf');
    //Previes del pdf
    Route::get('/alumnos/pdf/preview', [AlumnoController::class, 'previewPdf'])->name('alumnos.pdf.preview');
    
    
    //Faltas
    Route::get('/faltas', [FaltaController::class, 'index'])->name('faltas');
    Route::post('/faltas',[FaltaController::class,'store'])->name('store');
    Route::put('/faltas/{id_falta}',[FaltaController::class,'update'])->name('update');
    Route::delete('/faltas/{id_falta}',[FaltaController::class,'destroy'])->name('destroy');
    //Vistga show para ver las faltas
    Route::get('/faltas/show', [FaltaController::class, 'show'])->name('faltas.show');

    //Justificante pdf
    Route::post('/faltas/{falta}/justificante', [FaltaController::class, 'uploadJustificante'])
    ->name('faltas.justificante.upload');
});

Route::middleware('auth', 'verified', 'role:super_admin,admin')->group(function () {
    //USUARIOS
    Route::get('/users', [UserController::class, 'index'])->name('users'); 
    //Crear usuarios
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});

require __DIR__.'/settings.php';
