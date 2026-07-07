<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('cursos');

        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_curso');
            $table->string('profesor');
            $table->string('email');
            $table->string('aula');
            $table->text('temario')->nullable();
            $table->enum('turno', ['manana', 'tarde']);
            $table->date('dia_comienzo');
            $table->date('dia_final');
            $table->string('tecnico');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
