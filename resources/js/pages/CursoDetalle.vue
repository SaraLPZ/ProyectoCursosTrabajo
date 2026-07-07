<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

interface Curso {
    id: number;
    nombre: string;
    profesor: string;
    dia_comienzo: string;
    dia_final: string;
    aula: string;
    turno: string;
    requisitos?: string;
    descripcion?: string;
    pliego?: PliegoItem[];
}

interface PliegoItem {
    id: number;
    nombre: string;
    tipo: 'software' | 'material';
    procedencia?: 'centro' | 'alumno' | 'licencia' | 'gratuito';
    completado: boolean;
}

defineProps<{
    curso: Curso;
}>();
</script>

<template>
    <Head :title="curso.nombre" />

    <div class="mx-auto max-w-6xl px-6 py-8">
        <!-- BREADCRUMB -->
        <nav class="mb-4 flex items-center gap-2 text-sm text-gray-400">
            <Link href="/cursos" class="transition-colors hover:text-gray-600"
                >Cursos</Link
            >
            <span>/</span>
            <span class="text-gray-500">{{ curso.nombre }}</span>
        </nav>

        <!-- CABECERA -->
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-medium tracking-tight text-gray-900">
                    {{ curso.nombre }}
                </h1>
                <p class="mt-1 text-sm text-gray-400">
                    Información detallada y configuración del curso
                </p>
            </div>
            <span
                class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-green-50 px-3 py-1 text-xs font-medium text-green-700"
            >
                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                Activo
            </span>
        </div>

        <!-- MÉTRICAS -->
        <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-lg bg-gray-50 px-4 py-3">
                <p
                    class="mb-1 flex items-center gap-1.5 text-xs tracking-wide text-gray-400 uppercase"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>
                    Profesor
                </p>
                <p class="text-sm font-medium text-gray-800">
                    {{ curso.profesor }}
                </p>
            </div>
            <div class="rounded-lg bg-gray-50 px-4 py-3">
                <p
                    class="mb-1 flex items-center gap-1.5 text-xs tracking-wide text-gray-400 uppercase"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                        />
                    </svg>
                    Aula
                </p>
                <p class="text-sm font-medium text-gray-800">
                    {{ curso.aula }}
                </p>
            </div>
            <div class="rounded-lg bg-gray-50 px-4 py-3">
                <p
                    class="mb-1 flex items-center gap-1.5 text-xs tracking-wide text-gray-400 uppercase"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 3v1m0 16v1m8.66-9h-1M4.34 12h-1m15.07-6.07-.71.71M5.64 18.36l-.71.71M18.36 18.36l.71.71M5.64 5.64l.71.71M12 7a5 5 0 100 10A5 5 0 0012 7z"
                        />
                    </svg>
                    Turno
                </p>
                <p class="text-sm font-medium text-gray-800">
                    {{ curso.turno === 'manana' ? 'Mañana' : 'Tarde' }}
                </p>
            </div>
            <div class="rounded-lg bg-gray-50 px-4 py-3">
                <p
                    class="mb-1 flex items-center gap-1.5 text-xs tracking-wide text-gray-400 uppercase"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    Periodo
                </p>
                <p class="text-sm font-medium text-gray-800">
                    {{ curso.dia_comienzo }} — {{ curso.dia_final }}
                </p>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
            <!-- COLUMNA IZQUIERDA -->
            <div class="space-y-4 lg:col-span-3">
                <!-- Descripción -->
                <section class="rounded-lg border border-gray-100 p-5">
                    <h2
                        class="mb-3 flex items-center gap-2 text-sm font-medium text-gray-700"
                    >
                        <svg
                            class="h-4 w-4 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        Descripción
                    </h2>
                    <p class="text-sm leading-relaxed text-gray-500">
                        {{
                            curso.descripcion ||
                            'No se ha definido una descripción para este curso.'
                        }}
                    </p>
                </section>

                <!-- Pliego formativo -->
                <section class="rounded-lg border border-gray-100 p-5">
                    <h2
                        class="mb-4 flex items-center gap-2 text-sm font-medium text-gray-700"
                    >
                        <svg class="h-4 w-4 text-gray-400" />
                        Pliego formativo
                    </h2>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Software -->
                        <div
                            class="overflow-hidden rounded-md border border-gray-100"
                        >
                            <div
                                class="flex items-center gap-2 border-b border-blue-100 bg-blue-50 px-3 py-2"
                            >
                                <svg class="h-4 w-4 text-blue-800" />
                                <span class="text-xs font-medium text-blue-800"
                                    >Software</span
                                >
                            </div>
                            <ul class="divide-y divide-gray-50 p-3">
                                <li
                                    v-for="item in curso.pliego?.filter(
                                        (i) => i.tipo === 'software',
                                    )"
                                    :key="item.id"
                                    class="flex items-center justify-between py-2"
                                >
                                    <div class="flex items-center gap-2">
                                        <CheckIcon
                                            v-if="item.completado"
                                            class="h-4 w-4 text-green-500"
                                        />
                                        <CircleDashedIcon
                                            v-else
                                            class="h-4 w-4 text-gray-300"
                                        />
                                        <span
                                            class="text-sm"
                                            :class="
                                                item.completado
                                                    ? 'text-gray-800'
                                                    : 'text-gray-400'
                                            "
                                        >
                                            {{ item.nombre }}
                                        </span>
                                    </div>
                                    <span
                                        class="rounded bg-blue-50 px-2 py-0.5 text-xs text-blue-700"
                                    >
                                        {{ item.procedencia }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <!-- Material de taller -->
                        <div
                            class="overflow-hidden rounded-md border border-gray-100"
                        >
                            <div
                                class="flex items-center gap-2 border-b border-amber-100 bg-amber-50 px-3 py-2"
                            >
                                <svg class="h-4 w-4 text-amber-800" />
                                <span class="text-xs font-medium text-amber-800"
                                    >Material de taller</span
                                >
                            </div>
                            <ul class="divide-y divide-gray-50 p-3">
                                <li
                                    v-for="item in curso.pliego?.filter(
                                        (i) => i.tipo === 'material',
                                    )"
                                    :key="item.id"
                                    class="flex items-center justify-between py-2"
                                >
                                    <div class="flex items-center gap-2">
                                        <CheckIcon
                                            v-if="item.completado"
                                            class="h-4 w-4 text-green-500"
                                        />
                                        <CircleDashedIcon
                                            v-else
                                            class="h-4 w-4 text-gray-300"
                                        />
                                        <span
                                            class="text-sm"
                                            :class="
                                                item.completado
                                                    ? 'text-gray-800'
                                                    : 'text-gray-400'
                                            "
                                        >
                                            {{ item.nombre }}
                                        </span>
                                    </div>
                                    <span
                                        class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600"
                                    >
                                        {{ item.procedencia }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>

            <!-- COLUMNA DERECHA -->
            <div class="space-y-4 lg:col-span-2">
                <!-- Requisitos -->
                <section class="rounded-lg border border-gray-100 p-5">
                    <h2
                        class="mb-3 flex items-center gap-2 text-sm font-medium text-gray-700"
                    >
                        <svg
                            class="h-4 w-4 text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        Requisitos
                    </h2>
                    <p class="text-sm leading-relaxed text-gray-500">
                        {{
                            curso.requisitos ||
                            'No existen requisitos definidos.'
                        }}
                    </p>
                </section>

                <!-- Documentación -->
                <section class="rounded-lg border border-gray-100 p-5">
                    <h2
                        class="mb-3 flex items-center gap-2 text-sm font-medium text-gray-700"
                    >
                        <svg
                            class="h-4 w-4 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                            />
                        </svg>
                        Documentación
                    </h2>
                    <div
                        class="rounded-md border border-dashed border-gray-200 py-6 text-center"
                    >
                        <svg
                            class="mx-auto mb-2 h-5 w-5 text-gray-300"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                            />
                        </svg>
                        <p class="text-xs text-gray-400">
                            Sin archivos adjuntos
                        </p>
                    </div>
                </section>
            </div>
        </div>

        <!-- ACCIONES -->
        <div class="mt-6 flex justify-end gap-2 border-t border-gray-100 pt-4">
            <Link
                href="#"
                class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 px-3 py-1.5 text-sm text-gray-600 transition-colors hover:bg-gray-50"
            >
                <svg
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                    />
                </svg>
                Editar curso
            </Link>
            <button
                class="inline-flex items-center gap-1.5 rounded-md border border-red-100 bg-red-50 px-3 py-1.5 text-sm text-red-600 transition-colors hover:bg-red-100"
            >
                <svg
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                </svg>
                Eliminar
            </button>
        </div>
    </div>
</template>
