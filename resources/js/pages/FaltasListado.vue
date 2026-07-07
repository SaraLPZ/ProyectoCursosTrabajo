<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { SquarePen } from '@lucide/vue';

interface Curso {
    id: number;
    nombre_curso: string;
}

interface Alumno {
    id: number;
    nombre: string;
    apellidos: string;
    curso?: Curso;
}

interface Falta {
    id: number;
    alumno_id: number;
    fecha: string;
    justificada: boolean;
    observaciones?: string;
    alumno: Alumno;
    user?: User;
    justificante_pdf?: string | null;
}

interface User {
    id: number;
    name: string;
}
//Preview para los justificantes
const previewUrl = ref<string | null>(null);

function abrirPreviewJustificante(url: string) {
    previewUrl.value = url;
}

function cerrarPreviewJustificante() {
    previewUrl.value = null;
}

//Justificante
const archivos = ref<Record<number, File | null>>({});
const editandoJustificante = ref<number | null>(null);
const inputReemplazo = ref<HTMLInputElement | null>(null);

function subirAlSeleccionar(e: Event, faltaId: number) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) return;

    archivos.value[faltaId] = file;

    const formData = new FormData();
    formData.append('justificante_pdf', file);

    router.post(`/faltas/${faltaId}/justificante`, formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            archivos.value[faltaId] = null;

            // opcional: limpiar input para permitir re-subir el mismo archivo
            target.value = '';
        },
    });
}
//Si el administrativo se equivoca que lo edite, pero que este mas atento
function abrirReemplazo(faltaId: number) {
    editandoJustificante.value = faltaId;
    inputReemplazo.value?.click();
}

//reemplazar el justificante
function reemplazarJustificante(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file || editandoJustificante.value === null) return;

    const faltaId = editandoJustificante.value;

    const formData = new FormData();
    formData.append('justificante_pdf', file);

    router.post(`/faltas/${faltaId}/justificante`, formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            editandoJustificante.value = null;
            target.value = '';
        },
    });
}

const props = defineProps<{
    faltas: Falta[];
}>();

const page = usePage();

const filtroAlumno = ref('');
const filtroMes = ref('');
const filtroCurso = ref('');

const cursosDisponibles = computed(() => {
    const nombres = new Map<number, string>();
    for (const f of props.faltas) {
        const curso = f.alumno?.curso;
        if (curso) nombres.set(curso.id, curso.nombre_curso);
    }
    return Array.from(nombres.entries()).map(([id, nombre_curso]) => ({
        id,
        nombre_curso,
    }));
});

const mesesDisponibles = computed(() => {
    const meses = new Set<string>();
    for (const f of props.faltas) {
        meses.add(f.fecha.slice(0, 7)); // 'YYYY-MM'
    }
    return Array.from(meses).sort().reverse();
});

function formatoMes(yyyyMm: string) {
    const [anio, mes] = yyyyMm.split('-');
    const fecha = new Date(Number(anio), Number(mes) - 1, 1);
    const texto = fecha.toLocaleDateString('es-ES', {
        month: 'long',
        year: 'numeric',
    });
    return texto.charAt(0).toUpperCase() + texto.slice(1);
}

function formatoFecha(fecha: string) {
    const d = new Date(fecha + 'T00:00:00');
    return d.toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

const faltasFiltradas = computed(() => {
    const q = filtroAlumno.value.trim().toLowerCase();
    return props.faltas.filter((f) => {
        const nombreCompleto =
            `${f.alumno?.nombre ?? ''} ${f.alumno?.apellidos ?? ''}`.toLowerCase();
        const coincideAlumno = !q || nombreCompleto.includes(q);
        const coincideMes =
            !filtroMes.value || f.fecha.startsWith(filtroMes.value);
        const coincideCurso =
            !filtroCurso.value ||
            String(f.alumno?.curso?.id ?? '') === filtroCurso.value;
        return coincideAlumno && coincideMes && coincideCurso;
    });
});

function limpiarFiltros() {
    filtroAlumno.value = '';
    filtroMes.value = '';
    filtroCurso.value = '';
}

/* ----------------------------------------------------------------
 * MODAL DE CONFIRMACIÓN
 * ---------------------------------------------------------------- */
const faltaAEliminar = ref<Falta | null>(null);
const eliminando = ref(false);

function pedirConfirmacion(falta: Falta) {
    faltaAEliminar.value = falta;
}

function cancelarEliminacion() {
    if (eliminando.value) return;
    faltaAEliminar.value = null;
}

function confirmarEliminacion() {
    if (!faltaAEliminar.value) return;
    eliminando.value = true;

    router.delete(`/faltas/${faltaAEliminar.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            faltaAEliminar.value = null;
        },
        onFinish: () => {
            eliminando.value = false;
        },
    });
}

/* ----------------------------------------------------------------
 * TOAST FLOTANTE (no afecta al flujo del documento -> no descuadra nada)
 * ---------------------------------------------------------------- */
const mensaje = ref<{ tipo: 'success' | 'error'; texto: string } | null>(null);
let timeoutId: ReturnType<typeof setTimeout> | null = null;

function mostrarMensaje(tipo: 'success' | 'error', texto: string) {
    mensaje.value = { tipo, texto };
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        mensaje.value = null;
    }, 4000);
}

watch(
    () => page.props.flash as { success?: string; error?: string } | undefined,
    (flash) => {
        if (flash?.success) mostrarMensaje('success', flash.success);
        if (flash?.error) mostrarMensaje('error', flash.error);
    },
    { deep: true, immediate: true },
);
</script>

<template>
    <div
        class="min-h-screen bg-slate-50 px-4 py-10 font-sans text-slate-900 sm:px-8 lg:px-14"
    >
        <!-- TOAST FLOTANTE: position fixed, fuera del flujo del documento.
             No empuja ningún contenido al aparecer/desaparecer. -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-all duration-200 ease-out"
                leave-active-class="transition-all duration-150 ease-in"
                enter-from-class="opacity-0 -translate-y-2"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div
                    v-if="mensaje"
                    class="fixed top-6 left-1/2 z-50 flex -translate-x-1/2 items-center gap-3 rounded-xl border px-4 py-3 text-sm shadow-lg"
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
        </Teleport>

        <!-- MODAL DE CONFIRMACIÓN -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-150 ease-out"
                leave-active-class="transition-opacity duration-100 ease-in"
                enter-from-class="opacity-0"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="faltaAEliminar"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4"
                    @click.self="cancelarEliminacion"
                >
                    <Transition
                        appear
                        enter-active-class="transition-all duration-150 ease-out"
                        enter-from-class="opacity-0 scale-95"
                    >
                        <div
                            class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl"
                            role="alertdialog"
                            aria-modal="true"
                        >
                            <div
                                class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 text-orange-700"
                            >
                                <svg
                                    width="22"
                                    height="22"
                                    viewBox="0 0 16 16"
                                    fill="none"
                                >
                                    <path
                                        d="M3 4.5H13M6 4.5V3.2C6 2.8 6.3 2.5 6.7 2.5H9.3C9.7 2.5 10 2.8 10 3.2V4.5M11.5 4.5L11 12.8C11 13.2 10.6 13.5 10.2 13.5H5.8C5.4 13.5 5 13.2 5 12.8L4.5 4.5"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </div>

                            <h2
                                class="font-serif text-lg font-semibold text-slate-900"
                            >
                                ¿Eliminar esta falta?
                            </h2>
                            <p class="mt-2 text-sm text-slate-600">
                                Vas a eliminar la falta del día
                                <strong class="font-semibold text-slate-900">{{
                                    formatoFecha(faltaAEliminar.fecha)
                                }}</strong>
                                del alumno
                                <strong class="font-semibold text-slate-900"
                                    >{{ faltaAEliminar.alumno?.nombre }}
                                    {{
                                        faltaAEliminar.alumno?.apellidos
                                    }}</strong
                                >. Esta acción no se puede deshacer.
                            </p>

                            <div class="mt-6 flex justify-end gap-3">
                                <button
                                    type="button"
                                    :disabled="eliminando"
                                    class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                                    @click="cancelarEliminacion"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="button"
                                    :disabled="eliminando"
                                    class="inline-flex items-center gap-2 rounded-lg bg-orange-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-orange-800 disabled:cursor-not-allowed disabled:opacity-60"
                                    @click="confirmarEliminacion"
                                >
                                    {{
                                        eliminando
                                            ? 'Eliminando…'
                                            : 'Sí, eliminar'
                                    }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>

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
                    Faltas registradas
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ faltasFiltradas.length }}
                    {{
                        faltasFiltradas.length === 1
                            ? 'falta encontrada'
                            : 'faltas encontradas'
                    }}
                    <span v-if="faltasFiltradas.length !== faltas.length">
                        de {{ faltas.length }} en total
                    </span>
                </p>
            </div>

            <Link
                href="/faltas"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-900 transition-colors hover:border-emerald-700 hover:bg-emerald-50"
            >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path
                        d="M10 3L5 8L10 13"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
                Volver a asistencia
            </Link>
        </header>

        <!-- FILTROS -->
        <div
            class="mb-5 grid grid-cols-1 gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-4"
        >
            <label class="flex flex-col gap-1.5">
                <span class="text-xs font-semibold text-slate-500">Alumno</span>
                <input
                    v-model="filtroAlumno"
                    type="text"
                    placeholder="Nombre o apellidos…"
                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                />
            </label>

            <label class="flex flex-col gap-1.5">
                <span class="text-xs font-semibold text-slate-500">Mes</span>
                <select
                    v-model="filtroMes"
                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                >
                    <option value="">Todos los meses</option>
                    <option v-for="m in mesesDisponibles" :key="m" :value="m">
                        {{ formatoMes(m) }}
                    </option>
                </select>
            </label>

            <label class="flex flex-col gap-1.5">
                <span class="text-xs font-semibold text-slate-500">Curso</span>
                <select
                    v-model="filtroCurso"
                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                >
                    <option value="">Todos los cursos</option>
                    <option
                        v-for="c in cursosDisponibles"
                        :key="c.id"
                        :value="String(c.id)"
                    >
                        {{ c.nombre_curso }}
                    </option>
                </select>
            </label>

            <div class="flex items-end">
                <button
                    type="button"
                    @click="limpiarFiltros"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:border-slate-300 hover:bg-slate-50"
                >
                    Limpiar filtros
                </button>
            </div>
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
                            class="border-b border-slate-200 px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Curso
                        </th>
                        <th
                            class="border-b border-slate-200 px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Fecha
                        </th>
                        <th
                            class="border-b border-slate-200 px-5 py-3.5 text-center text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Justificada
                        </th>
                        <th
                            class="border-b border-slate-200 px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Justificante PDF
                        </th>
                        <th
                            class="w-px border-b border-slate-200 px-5 py-3.5 text-right text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Registrado por
                        </th>
                        <th
                            class="w-px border-b border-slate-200 px-5 py-3.5 text-right text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="faltasFiltradas.length === 0">
                        <td colspan="6" class="px-5 py-4">
                            <div
                                class="flex flex-col items-center gap-3 py-12 text-center text-slate-500"
                            >
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-500"
                                >
                                    <svg
                                        width="28"
                                        height="28"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                    >
                                        <path
                                            d="M5 19c0-3.3 3.1-6 7-6s7 2.7 7 6"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                        />
                                        <circle
                                            cx="12"
                                            cy="8"
                                            r="3.2"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        />
                                    </svg>
                                </div>
                                <p class="text-sm">
                                    Ninguna falta coincide con los filtros
                                    aplicados
                                </p>
                                <button
                                    type="button"
                                    class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-900 transition-colors hover:border-emerald-700 hover:bg-emerald-50"
                                    @click="limpiarFiltros"
                                >
                                    Limpiar filtros
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr
                        v-for="falta in faltasFiltradas"
                        :key="falta.id"
                        class="hover:bg-slate-50"
                    >
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 align-middle text-sm font-medium text-slate-900"
                        >
                            {{ falta.alumno?.nombre }}
                            {{ falta.alumno?.apellidos }}
                        </td>
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 align-middle text-sm"
                        >
                            <span
                                v-if="falta.alumno?.curso"
                                class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800"
                            >
                                {{ falta.alumno.curso.nombre_curso }}
                            </span>
                            <span v-else class="text-slate-400">—</span>
                        </td>
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 align-middle text-sm text-slate-600"
                        >
                            {{ formatoFecha(falta.fecha) }}
                        </td>
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 text-center align-middle"
                        >
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                :class="
                                    falta.justificada
                                        ? 'bg-emerald-100 text-emerald-800'
                                        : 'bg-slate-100 text-slate-600'
                                "
                            >
                                {{ falta.justificada ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 text-sm"
                        >
                            <!-- SI YA EXISTE PDF -->
                            <div
                                v-if="falta.justificante_pdf"
                                class="flex items-center gap-2"
                            >
                                <button
                                    type="button"
                                    class="font-medium text-emerald-700 hover:underline"
                                    @click="
                                        abrirPreviewJustificante(
                                            `/storage/${falta.justificante_pdf}`,
                                        )
                                    "
                                >
                                    Ver justificante
                                </button>
                                <span
                                    class="cursor-pointer text-slate-500 transition-colors duration-200 hover:text-slate-700"
                                    title="Cambiar justificante"
                                    @click="abrirReemplazo(falta.id)"
                                >
                                    <SquarePen class="h-4 w-4" />
                                </span>
                            </div>

                            <!-- SI NO EXISTE -->
                            <div v-else class="flex items-center gap-2">
                                <input
                                    :id="`file-${falta.id}`"
                                    type="file"
                                    accept="application/pdf"
                                    class="hidden"
                                    @change="
                                        (e: Event) =>
                                            subirAlSeleccionar(e, falta.id)
                                    "
                                />
                                <label
                                    :for="`file-${falta.id}`"
                                    class="cursor-pointer rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50"
                                >
                                    {{
                                        archivos[falta.id]?.name ??
                                        'Seleccionar PDF'
                                    }}
                                </label>
                            </div>
                        </td>
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 align-middle text-sm font-medium text-slate-900"
                        >
                            <span v-if="falta.user" class="text-slate-900">
                                {{ falta.user.name }}
                            </span>
                            <span v-else class="text-slate-400">-</span>
                        </td>
                        <td
                            class="w-px border-b border-slate-200 px-5 py-3.5 text-right align-middle"
                        >
                            <button
                                type="button"
                                class="inline-flex h-[34px] w-[34px] items-center justify-center rounded-lg border border-slate-200 bg-white text-orange-700 transition-colors hover:border-orange-700 hover:bg-orange-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700"
                                title="Eliminar falta"
                                @click="pedirConfirmacion(falta)"
                            >
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 16 16"
                                    fill="none"
                                >
                                    <path
                                        d="M3 4.5H13M6 4.5V3.2C6 2.8 6.3 2.5 6.7 2.5H9.3C9.7 2.5 10 2.8 10 3.2V4.5M11.5 4.5L11 12.8C11 13.2 10.6 13.5 10.2 13.5H5.8C5.4 13.5 5 13.2 5 12.8L4.5 4.5"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- PANEL PREVIEW JUSTIFICANTE -->
        <Transition
            enter-active-class="transition-transform duration-300 ease-out"
            leave-active-class="transition-transform duration-200 ease-in"
            enter-from-class="translate-x-full"
            leave-to-class="translate-x-full"
        >
            <div
                v-if="previewUrl"
                class="fixed inset-y-0 right-0 z-50 flex w-full max-w-2xl flex-col bg-white shadow-2xl"
            >
                <div
                    class="flex items-center justify-between border-b border-slate-200 px-6 py-4"
                >
                    <div>
                        <p
                            class="text-xs font-semibold tracking-widest text-emerald-700 uppercase"
                        >
                            Vista previa
                        </p>
                        <h2
                            class="font-serif text-lg font-semibold text-slate-900"
                        >
                            Justificante PDF
                        </h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <a
                            :href="previewUrl"
                            download
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-emerald-800"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v12m0 0l-4-4m4 4l4-4M6 20h12"
                                />
                            </svg>
                            Descargar
                        </a>
                        <button
                            @click="cerrarPreviewJustificante"
                            class="hover:text-slatealar-900 rounded-md p-1.5 text-slate-500 transition-colors hover:bg-slate-100"
                            aria-label="Cerrar vista previa"
                        >
                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 18 18"
                                fill="none"
                            >
                                <path
                                    d="M4 4L14 14M14 4L4 14"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
                <iframe
                    :src="previewUrl"
                    class="h-full w-full flex-1 border-none"
                    title="Vista previa del justificante"
                />
            </div>
        </Transition>

        <!-- Overlay -->
        <Transition
            enter-active-class="transition-opacity duration-300 ease-out"
            leave-active-class="transition-opacity duration-200 ease-in"
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
        >
            <div
                v-if="previewUrl"
                class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-[2px]"
                @click="cerrarPreviewJustificante"
            />
        </Transition>
        <input
            type="file"
            ref="inputReemplazo"
            accept="application/pdf"
            class="hidden"
            @change="reemplazarJustificante"
        />
    </div>
</template>
