<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Alumno {
    id: number;
    nombre: string;
    apellidos: string;
    faltas_no_justificadas_count: number;
}

const props = defineProps<{
    alumnos: Alumno[];
}>();

const UMBRAL_AVISO = 2;
const UMBRAL_EXPULSION = 4;

const form = useForm({
    fecha: new Date().toISOString().split('T')[0],
    faltas: [] as {
        alumno_id: number;
        falta: boolean;
        justificada: boolean;
    }[],
});

function resetFaltas() {
    form.faltas = props.alumnos.map((alumno) => ({
        alumno_id: alumno.id,
        falta: false,
        justificada: false,
    }));
}
resetFaltas();

const mensaje = ref<{ tipo: 'success' | 'error'; texto: string } | null>(null);

const seleccionados = computed(() => form.faltas.filter((f) => f.falta).length);

const fechaLegible = computed(() => {
    if (!form.fecha) return '';
    const d = new Date(form.fecha + 'T00:00:00');
    return d.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    });
});

function iniciales(alumno: Alumno) {
    const a = alumno.nombre?.[0] ?? '';
    const b = alumno.apellidos?.[0] ?? '';
    return (a + b).toUpperCase();
}

function nivelAviso(count: number) {
    if (count >= UMBRAL_EXPULSION) return 'expulsado';
    if (count >= UMBRAL_AVISO) return 'critico';
    if (count === UMBRAL_AVISO - 1) return 'cerca';
    return 'normal';
}

function guardar() {
    if (seleccionados.value === 0) {
        mensaje.value = {
            tipo: 'error',
            texto: 'Marca al menos un alumno como ausente antes de guardar.',
        };
        return;
    }

    form.post('/faltas', {
        preserveScroll: true,
        onSuccess: () => {
            mensaje.value = {
                tipo: 'success',
                texto: `Asistencia guardada: ${seleccionados.value} ${
                    seleccionados.value === 1
                        ? 'falta registrada'
                        : 'faltas registradas'
                }.`,
            };
            resetFaltas();
        },
        onError: (errors) => {
            mensaje.value = {
                tipo: 'error',
                texto: 'No se pudo guardar. Revisa los datos e inténtalo de nuevo.',
            };
            console.log(errors);
        },
    });
}
</script>

<template>
    <div
        class="min-h-screen bg-slate-50 px-4 py-10 font-sans text-slate-900 sm:px-8 lg:px-14"
    >
        <!-- HEADER -->
        <header class="mb-8 flex flex-wrap items-end justify-between gap-6">
            <div>
                <p
                    class="mb-1 text-xs font-semibold tracking-[0.12em] text-emerald-700 uppercase"
                >
                    Secretaría académica
                </p>
                <h1
                    class="font-serif text-3xl font-semibold tracking-tight text-slate-900"
                >
                    Control de asistencia
                </h1>
                <p class="mt-1 text-sm text-slate-500 capitalize">
                    {{ fechaLegible }}
                </p>
            </div>

            <div class="flex items-end gap-3">
                <Link
                    href="/faltas/show"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-900 transition-colors hover:border-emerald-700 hover:bg-emerald-50"
                >
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <rect
                            x="2.5"
                            y="3"
                            width="11"
                            height="10"
                            rx="1.5"
                            stroke="currentColor"
                            stroke-width="1.5"
                        />
                        <path
                            d="M5 6.5H11M5 9H11"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                        />
                    </svg>
                    Faltas registradas
                </Link>

                <label class="flex flex-col gap-1.5 text-right">
                    <span class="text-xs font-semibold text-slate-500"
                        >Fecha del registro</span
                    >
                    <input
                        type="date"
                        v-model="form.fecha"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                    />
                </label>
            </div>
        </header>

        <!-- MENSAJE DE ESTADO -->
        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            leave-active-class="transition-all duration-150 ease-in"
            enter-from-class="opacity-0 -translate-y-1"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <div
                v-if="mensaje"
                class="mb-5 flex items-center gap-3 rounded-xl border px-4 py-3 text-sm"
                :class="
                    mensaje.tipo === 'success'
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                        : 'border-orange-200 bg-orange-50 text-orange-800'
                "
            >
                <svg
                    v-if="mensaje.tipo === 'success'"
                    width="18"
                    height="18"
                    viewBox="0 0 18 18"
                    fill="none"
                    class="flex-shrink-0"
                >
                    <circle
                        cx="9"
                        cy="9"
                        r="7.5"
                        stroke="currentColor"
                        stroke-width="1.5"
                    />
                    <path
                        d="M5.5 9.2L7.7 11.5L12.5 6.5"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
                <svg
                    v-else
                    width="18"
                    height="18"
                    viewBox="0 0 18 18"
                    fill="none"
                    class="flex-shrink-0"
                >
                    <path
                        d="M9 2L16.5 15H1.5L9 2Z"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linejoin="round"
                    />
                    <path
                        d="M9 7V10.5"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                    />
                    <circle cx="9" cy="12.7" r="0.9" fill="currentColor" />
                </svg>
                {{ mensaje.texto }}
            </div>
        </Transition>

        <!-- RESUMEN -->
        <div
            class="mb-5 flex flex-wrap items-center gap-3 text-sm text-slate-500"
        >
            <span>
                {{ alumnos.length }}
                {{ alumnos.length === 1 ? 'alumno' : 'alumnos' }}
            </span>
            <span class="text-slate-300">·</span>
            <span>
                <strong class="font-semibold text-slate-900">{{
                    seleccionados
                }}</strong>
                {{
                    seleccionados === 1
                        ? 'ausencia marcada hoy'
                        : 'ausencias marcadas hoy'
                }}
            </span>
        </div>

        <!-- TABLE CARD -->
        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50/80">
                        <th
                            class="border-b border-slate-200 px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Alumno
                        </th>
                        <th
                            class="border-b border-slate-200 px-5 py-3.5 text-center text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Falta hoy
                        </th>
                        <th
                            class="border-b border-slate-200 px-5 py-3.5 text-center text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Justificada
                        </th>
                        <th
                            class="border-b border-slate-200 px-5 py-3.5 text-center text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Faltas sin justificar
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="(registro, index) in form.faltas"
                        :key="registro.alumno_id"
                        class="hover:bg-slate-50"
                    >
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 align-middle text-sm"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800"
                                >
                                    {{ iniciales(props.alumnos[index]) }}
                                </span>
                                <span class="font-medium text-slate-900">
                                    {{ props.alumnos[index].nombre }}
                                    {{ props.alumnos[index].apellidos }}
                                </span>
                            </div>
                        </td>

                        <td
                            class="border-b border-slate-200 px-5 py-3.5 text-center align-middle"
                        >
                            <input
                                type="checkbox"
                                v-model="registro.falta"
                                class="h-4 w-4 cursor-pointer rounded border-slate-300 text-emerald-700 focus:ring-emerald-700/30"
                            />
                        </td>

                        <td
                            class="border-b border-slate-200 px-5 py-3.5 text-center align-middle"
                        >
                            <input
                                type="checkbox"
                                v-model="registro.justificada"
                                :disabled="!registro.falta"
                                class="h-4 w-4 cursor-pointer rounded border-slate-300 text-emerald-700 focus:ring-emerald-700/30 disabled:cursor-not-allowed disabled:opacity-40"
                            />
                        </td>

                        <td
                            class="border-b border-slate-200 px-5 py-3.5 text-center align-middle"
                        >
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                                :class="{
                                    'bg-red-100 text-red-700':
                                        nivelAviso(
                                            props.alumnos[index]
                                                .faltas_no_justificadas_count,
                                        ) === 'expulsado',
                                    'bg-orange-100 text-orange-700':
                                        nivelAviso(
                                            props.alumnos[index]
                                                .faltas_no_justificadas_count,
                                        ) === 'critico',
                                    'bg-amber-100 text-amber-800':
                                        nivelAviso(
                                            props.alumnos[index]
                                                .faltas_no_justificadas_count,
                                        ) === 'cerca',
                                    'bg-slate-100 text-slate-600':
                                        nivelAviso(
                                            props.alumnos[index]
                                                .faltas_no_justificadas_count,
                                        ) === 'normal',
                                }"
                            >
                                <svg
                                    v-if="
                                        nivelAviso(
                                            props.alumnos[index]
                                                .faltas_no_justificadas_count,
                                        ) === 'critico' ||
                                        nivelAviso(
                                            props.alumnos[index]
                                                .faltas_no_justificadas_count,
                                        ) === 'expulsado'
                                    "
                                    width="13"
                                    height="13"
                                    viewBox="0 0 18 18"
                                    fill="none"
                                >
                                    <path
                                        d="M9 2L16.5 15H1.5L9 2Z"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M9 7V10.5"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                    />
                                    <circle
                                        cx="9"
                                        cy="12.7"
                                        r="0.9"
                                        fill="currentColor"
                                    />
                                </svg>
                                {{
                                    props.alumnos[index]
                                        .faltas_no_justificadas_count
                                }}
                                <span
                                    v-if="
                                        nivelAviso(
                                            props.alumnos[index]
                                                .faltas_no_justificadas_count,
                                        ) === 'expulsado'
                                    "
                                >
                                    · Expulsado
                                </span>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- LEYENDA -->
        <p class="mt-3 text-xs text-slate-400">
            El triángulo aparece a partir de {{ UMBRAL_AVISO }} faltas sin
            justificar. A partir de {{ UMBRAL_EXPULSION }}, el alumno se marca
            como expulsado del curso.
        </p>

        <!-- ACCIONES -->
        <div class="mt-6 flex justify-end">
            <button
                type="button"
                @click="guardar"
                :disabled="form.processing"
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-emerald-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                {{ form.processing ? 'Guardando…' : 'Guardar asistencia' }}
            </button>
        </div>
    </div>
</template>
