<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

//Controlar la preview del pdf
const showPreview = ref(false);

function openPreview() {
    showPreview.value = true;
}

function closePreview() {
    showPreview.value = false;
}

function firmasUrl(alumno: Alumno) {
    if (!alumno.curso) return null;
    return `/cursos/${alumno.curso.id}/firmas`;
}

interface Curso {
    id: number;
    nombre_curso: string;
}

interface Alumno {
    id: number;
    nombre: string;
    apellidos: string;
    dni: string;
    email?: string;
    telefono?: string;
    curso?: Curso;
}

const props = defineProps<{
    alumnos: Alumno[];
    cursos: Curso[];
}>();

const showModal = ref(false);
const search = ref('');

function openModal() {
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    form.reset();
}

const form = useForm({
    nombre: '',
    apellidos: '',
    dni: '',
    email: '',
    telefono: '',
    codigo_postal: '',
    f_nacimiento: '',
    curso_id: '',
});

function submit() {
    form.post('/alumnos', {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
    });
}

function eliminar(id: number) {
    if (confirm('¿Seguro que quieres eliminar este alumno?')) {
        router.delete(`/alumnos/${id}`);
    }
}

function iniciales(alumno: Alumno) {
    const a = alumno.nombre?.[0] ?? '';
    const b = alumno.apellidos?.[0] ?? '';
    return (a + b).toUpperCase();
}

// Paleta cíclica para distinguir cursos sin saturar la vista.
// Cada entrada trae las clases de avatar/badge y el color de foco del select de curso.
const courseTints = [
    {
        badge: 'bg-emerald-100 text-emerald-800',
        avatar: 'bg-emerald-100 text-emerald-800',
    },
    {
        badge: 'bg-orange-100 text-orange-700',
        avatar: 'bg-orange-100 text-orange-700',
    },
    {
        badge: 'bg-amber-100 text-amber-800',
        avatar: 'bg-amber-100 text-amber-800',
    },
    {
        badge: 'bg-blue-100 text-blue-800',
        avatar: 'bg-blue-100 text-blue-800',
    },
];
const mutedTint = {
    badge: 'bg-slate-200 text-slate-600',
    avatar: 'bg-slate-200 text-slate-600',
};

function courseTint(curso?: Curso) {
    if (!curso) return mutedTint;
    return courseTints[curso.id % courseTints.length];
}

const filteredAlumnos = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return props.alumnos;
    return props.alumnos.filter((a) =>
        [a.nombre, a.apellidos, a.dni, a.email]
            .filter(Boolean)
            .some((field) => field!.toLowerCase().includes(q)),
    );
});
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
                    Alumnos matriculados
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ alumnos.length }}
                    {{
                        alumnos.length === 1
                            ? 'alumno registrado'
                            : 'alumnos registrados'
                    }}
                </p>
            </div>

            <button
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-emerald-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 active:scale-[0.98]"
                @click="openModal"
            >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path
                        d="M8 3V13M3 8H13"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>
                Nuevo alumno
            </button>
            <!-- Botón previsualizar -->
            <button
                @click="openPreview"
                class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-900 shadow-sm transition-all duration-200 hover:border-emerald-700 hover:bg-emerald-50 active:scale-95"
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
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                    />
                </svg>
                Previsualizar PDF
            </button>

            <!-- Botón descargar -->
            <a
                href="/alumnos/pdf"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-white shadow-sm transition-all duration-200 hover:bg-slate-900 active:scale-95"
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
                Exportar PDF
            </a>
        </header>

        <!-- TOOLBAR -->
        <div class="mb-4">
            <div
                class="flex max-w-sm items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-slate-500 focus-within:border-emerald-700"
            >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <circle
                        cx="7"
                        cy="7"
                        r="5"
                        stroke="currentColor"
                        stroke-width="1.6"
                    />
                    <path
                        d="M11 11L14.5 14.5"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                    />
                </svg>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar por nombre, apellidos, DNI o email…"
                    class="w-full border-none bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400"
                />
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
                            DNI
                        </th>
                        <th
                            class="border-b border-slate-200 px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Contacto
                        </th>
                        <th
                            class="border-b border-slate-200 px-5 py-3.5 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Curso
                        </th>
                        <th
                            class="w-px border-b border-slate-200 px-5 py-3.5 text-right text-xs font-semibold tracking-wider text-slate-500 uppercase"
                        >
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="filteredAlumnos.length === 0">
                        <td colspan="5" class="px-5 py-4">
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
                                        <circle
                                            cx="12"
                                            cy="8"
                                            r="3.2"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        />
                                        <path
                                            d="M5 19c0-3.3 3.1-6 7-6s7 2.7 7 6"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </div>
                                <p v-if="search" class="text-sm">
                                    Ningún alumno coincide con
                                    <strong class="text-slate-900"
                                        >"{{ search }}"</strong
                                    >
                                </p>
                                <p v-else class="text-sm">
                                    Todavía no hay ningún alumno matriculado
                                </p>
                                <button
                                    v-if="!search"
                                    class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-900 transition-colors hover:border-emerald-700 hover:bg-emerald-50"
                                    @click="openModal"
                                >
                                    Matricular al primero
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr
                        v-for="alumno in filteredAlumnos"
                        :key="alumno.id"
                        class="hover:bg-slate-50"
                    >
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 align-middle text-sm"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                    :class="courseTint(alumno.curso).avatar"
                                >
                                    {{ iniciales(alumno) }}
                                </span>
                                <span class="font-medium text-slate-900">
                                    {{ alumno.nombre }} {{ alumno.apellidos }}
                                </span>
                            </div>
                        </td>
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 align-middle text-sm"
                        >
                            <span
                                class="font-mono text-[0.84rem] tracking-wide text-slate-500"
                                >{{ alumno.dni }}</span
                            >
                        </td>
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 align-middle text-sm"
                        >
                            <div class="flex flex-col gap-0.5">
                                <span
                                    v-if="alumno.email"
                                    class="text-slate-900"
                                    >{{ alumno.email }}</span
                                >
                                <span
                                    v-if="alumno.telefono"
                                    class="text-xs text-slate-500"
                                    >{{ alumno.telefono }}</span
                                >
                                <span
                                    v-if="!alumno.email && !alumno.telefono"
                                    class="text-slate-500"
                                    >—</span
                                >
                            </div>
                        </td>
                        <td
                            class="border-b border-slate-200 px-5 py-3.5 align-middle text-sm"
                        >
                            <span
                                v-if="alumno.curso"
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                :class="courseTint(alumno.curso).badge"
                            >
                                {{ alumno.curso.nombre_curso }}
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600"
                            >
                                Sin curso
                            </span>
                        </td>
                        <td
                            class="w-px border-b border-slate-200 px-5 py-3.5 text-right align-middle text-sm"
                        >
                            <div class="flex items-center justify-end gap-2">
                                <!-- Hoja de firmas -->
                                <a
                                    v-if="alumno.curso"
                                    :href="firmasUrl(alumno)!"
                                    target="_blank"
                                    class="inline-flex h-[34px] w-[34px] items-center justify-center rounded-lg border border-slate-200 bg-white text-emerald-700 transition-colors hover:border-emerald-700 hover:bg-emerald-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
                                    title="Hoja de firmas del curso"
                                >
                                    <svg
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                    >
                                        <path
                                            d="M9 12h6M9 16h4M7 4H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2h-2"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                        />
                                        <path
                                            d="M9 4h6a1 1 0 011 1v1H8V5a1 1 0 011-1z"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </a>

                                <!-- Eliminar -->
                                <button
                                    class="inline-flex h-[34px] w-[34px] items-center justify-center rounded-lg border border-slate-200 bg-white text-orange-700 transition-colors hover:border-orange-700 hover:bg-orange-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700"
                                    title="Eliminar alumno"
                                    @click="eliminar(alumno.id)"
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
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- MODAL -->
        <Transition
            enter-active-class="transition-opacity duration-200 ease-out"
            leave-active-class="transition-opacity duration-150 ease-in"
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/45 p-6 backdrop-blur-[2px]"
                @click.self="closeModal"
            >
                <div
                    class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl motion-safe:animate-[panel-in_0.2s_ease]"
                >
                    <div
                        class="flex items-start justify-between px-7 pt-6 pb-4"
                    >
                        <div>
                            <p
                                class="mb-1 text-xs font-semibold tracking-[0.12em] text-emerald-700 uppercase"
                            >
                                Nueva matrícula
                            </p>
                            <h2
                                class="font-serif text-xl font-semibold text-slate-900"
                            >
                                Datos del alumno
                            </h2>
                        </div>
                        <button
                            class="rounded-md p-1.5 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900"
                            aria-label="Cerrar"
                            @click="closeModal"
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

                    <form
                        class="flex flex-col gap-4 px-7 pb-7"
                        @submit.prevent="submit"
                    >
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="flex flex-col gap-1.5">
                                <span
                                    class="text-xs font-semibold text-slate-500"
                                    >Nombre</span
                                >
                                <input
                                    v-model="form.nombre"
                                    type="text"
                                    placeholder="María"
                                    required
                                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                                />
                            </label>
                            <label class="flex flex-col gap-1.5">
                                <span
                                    class="text-xs font-semibold text-slate-500"
                                    >Apellidos</span
                                >
                                <input
                                    v-model="form.apellidos"
                                    type="text"
                                    placeholder="García López"
                                    required
                                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                                />
                            </label>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="flex flex-col gap-1.5">
                                <span
                                    class="text-xs font-semibold text-slate-500"
                                    >DNI</span
                                >
                                <input
                                    v-model="form.dni"
                                    type="text"
                                    placeholder="12345678A"
                                    required
                                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                                />
                            </label>
                            <label class="flex flex-col gap-1.5">
                                <span
                                    class="text-xs font-semibold text-slate-500"
                                    >Fecha de nacimiento</span
                                >
                                <input
                                    v-model="form.f_nacimiento"
                                    type="date"
                                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                                />
                            </label>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="flex flex-col gap-1.5">
                                <span
                                    class="text-xs font-semibold text-slate-500"
                                    >Email</span
                                >
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="maria@ejemplo.com"
                                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                                />
                            </label>
                            <label class="flex flex-col gap-1.5">
                                <span
                                    class="text-xs font-semibold text-slate-500"
                                    >Teléfono</span
                                >
                                <input
                                    v-model="form.telefono"
                                    type="tel"
                                    placeholder="600 123 456"
                                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                                />
                            </label>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="flex flex-col gap-1.5">
                                <span
                                    class="text-xs font-semibold text-slate-500"
                                    >Código postal</span
                                >
                                <input
                                    v-model="form.codigo_postal"
                                    type="text"
                                    placeholder="28001"
                                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                                />
                            </label>
                            <label class="flex flex-col gap-1.5">
                                <span
                                    class="text-xs font-semibold text-slate-500"
                                    >Curso</span
                                >
                                <select
                                    v-model="form.curso_id"
                                    class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-shadow outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/10"
                                >
                                    <option value="">Selecciona curso</option>
                                    <option
                                        v-for="c in cursos"
                                        :key="c.id"
                                        :value="c.id"
                                    >
                                        {{ c.nombre_curso }}
                                    </option>
                                </select>
                            </label>
                        </div>

                        <div class="mt-1 flex justify-end gap-3">
                            <button
                                type="button"
                                class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-900 transition-colors hover:border-emerald-700 hover:bg-emerald-50"
                                @click="closeModal"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-emerald-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="form.processing"
                            >
                                {{
                                    form.processing
                                        ? 'Guardando…'
                                        : 'Guardar alumno'
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
        <!-- PANEL PREVISUALIZACIÓN PDF -->
        <Transition
            enter-active-class="transition-transform duration-300 ease-out"
            leave-active-class="transition-transform duration-200 ease-in"
            enter-from-class="translate-x-full"
            leave-to-class="translate-x-full"
        >
            <div
                v-if="showPreview"
                class="fixed inset-y-0 right-0 z-50 flex w-full max-w-2xl flex-col bg-white shadow-2xl"
            >
                <!-- Cabecera del panel -->
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
                            Listado de alumnos
                        </h2>
                    </div>

                    <div class="flex items-center gap-3">
                        <a
                            href="/alumnos/pdf"
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
                            @click="closePreview"
                            class="rounded-md p-1.5 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900"
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

                <!-- iframe con el PDF -->
                <iframe
                    src="/alumnos/pdf/preview"
                    class="h-full w-full flex-1 border-none"
                    title="Vista previa del PDF"
                />
            </div>
        </Transition>

        <!-- Overlay oscuro detrás del panel -->
        <Transition
            enter-active-class="transition-opacity duration-300 ease-out"
            leave-active-class="transition-opacity duration-200 ease-in"
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showPreview"
                class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-[2px]"
                @click="closePreview"
            />
        </Transition>
    </div>
</template>
