<script setup lang="ts">
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { cursos as cursosRoute } from '@/routes';
import { ref, watch, computed } from 'vue';
//Iconos
import {
    Trash2,
    SquarePen,
    Plus,
    X,
    GraduationCap,
    Search,
    Sun,
    Moon,
    User,
    MapPin,
    Calendar,
    MoreHorizontal,
    BookOpen,
} from '@lucide/vue';

//Crear curso
const showModal = ref(false);

const isEditing = ref(false);
const editingId = ref<number | null>(null);

function openModal() {
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    isEditing.value = false;
    editingId.value = null;
    form.reset();
}
//Formatear la fecha
function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

interface Curso {
    id: number;
    nombre_curso: string;
    profesor: string;
    email: string;
    aula: string;
    temario: string;
    turno: string;
    dia_comienzo: string;
    dia_final: string;
    tecnico: string;
}

const page = usePage();
const props = defineProps<{ cursos: Curso[] }>();

const search = ref('');

const filteredCursos = computed(() => {
    if (!search.value.trim()) return props.cursos;
    const q = search.value.toLowerCase();
    return props.cursos.filter((c) =>
        [c.nombre_curso, c.profesor, c.email, c.aula, c.tecnico]
            .filter(Boolean)
            .some((field) => field.toLowerCase().includes(q)),
    );
});

//Estadísticas
const stats = computed(() => {
    const total = props.cursos.length;
    const manana = props.cursos.filter((c) => c.turno === 'manana').length;
    const tarde = props.cursos.filter((c) => c.turno === 'tarde').length;
    const profesores = new Set(props.cursos.map((c) => c.profesor)).size;

    const hoy = new Date();
    const mesActual = hoy.getMonth();
    const anioActual = hoy.getFullYear();

    const finalizanEsteMes = props.cursos.filter((c) => {
        const fechaFin = new Date(c.dia_final);

        return (
            fechaFin.getMonth() === mesActual &&
            fechaFin.getFullYear() === anioActual
        );
    }).length;

    return {
        total,
        manana,
        tarde,
        profesores,
        finalizanEsteMes,
    };
});
//Filtro proximos cursos
const proximosCursos = computed(() => {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    return props.cursos
        .filter((curso) => {
            const inicio = new Date(curso.dia_comienzo);
            inicio.setHours(0, 0, 0, 0);

            return inicio >= hoy;
        })
        .sort(
            (a, b) =>
                new Date(a.dia_comienzo).getTime() -
                new Date(b.dia_comienzo).getTime(),
        );
});

const form = useForm({
    nombre_curso: '',
    profesor: '',
    email: '',
    aula: '',
    temario: '',
    turno: 'manana',
    dia_comienzo: '',
    dia_final: '',
    tecnico: '',
});

const successMessage = ref<string | null>(null);

watch(
    () => (page.props.flash as any)?.success,
    (value) => {
        if (!value) return;

        successMessage.value = value;

        setTimeout(() => {
            successMessage.value = null;
        }, 3000);
    },
    { immediate: true },
);

function submit() {
    if (isEditing.value && editingId.value) {
        form.put(`/cursos/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                closeModal();
                isEditing.value = false;
                editingId.value = null;
            },
        });
    } else {
        form.post('/cursos', {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                closeModal();
            },
        });
    }
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Cursos',
                href: cursosRoute(),
            },
        ],
    },
});
//Editar curso valores
function editCurso(curso: Curso) {
    isEditing.value = true;
    editingId.value = curso.id;

    form.nombre_curso = curso.nombre_curso;
    form.profesor = curso.profesor;
    form.email = curso.email;
    form.aula = curso.aula;
    form.temario = curso.temario;
    form.turno = curso.turno;
    form.dia_comienzo = curso.dia_comienzo;
    form.dia_final = curso.dia_final;
    form.tecnico = curso.tecnico;

    openModal();
}
//Eliminar curso
function destroy(id: number) {
    if (!confirm('¿Seguro que quieres eliminar este curso?')) return;

    form.delete(`/cursos/${id}`, {
        preserveScroll: true,
    });
}
//Si ha terminado el curso
function cursoFinalizado(fecha: string) {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    const fechaFin = new Date(fecha);
    fechaFin.setHours(0, 0, 0, 0);

    return fechaFin < hoy;
}
//Si ha empezado el curso
function cursoEmpezado(fecha: string) {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    const fechaInicio = new Date(fecha);
    fechaInicio.setHours(0, 0, 0, 0);

    return fechaInicio <= hoy;
}

//Obtener el curso de un aula
const turnoActual = computed(() => {
    const hora = new Date().getHours();

    if (hora >= 8 && hora < 15) return 'manana';
    if (hora >= 16 && hora < 21) return 'tarde';

    return null;
});

//Ver si el curso ha empezado o finalizado para directo
function cursoEnAula(aula: string) {
    if (!turnoActual.value) return null;

    return props.cursos.find(
        (curso) =>
            curso.aula.toUpperCase() === aula.toUpperCase() &&
            curso.turno === turnoActual.value &&
            cursoEmpezado(curso.dia_comienzo) &&
            !cursoFinalizado(curso.dia_final),
    );
}
//Variables aulas
const aulasPrimeraPlanta = [
    ['6', '5', '4', '3'],
    ['7', '8', '9'],
];

const aulasSegundaPlanta = [
    ['13', '12', '11', '10'],
    ['16', '15', '14'],
];

//Menú de acciones por fila
const openMenuId = ref<number | null>(null);
function toggleMenu(id: number) {
    openMenuId.value = openMenuId.value === id ? null : id;
}
function closeMenu() {
    openMenuId.value = null;
}
</script>

<template>
    <Head title="Cursos" />

    <div class="min-h-screen bg-slate-50 p-2 lg:p-2" @click="closeMenu">
        <div class="mx-auto flex items-start gap-[10px] p-[20px]">
            <div class="flex-1">
                <!-- FLASH -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-if="successMessage"
                        class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm"
                    >
                        <div
                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500"
                        >
                            <svg
                                class="h-3 w-3 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="3"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4.5 12.75l6 6 9-13.5"
                                />
                            </svg>
                        </div>
                        <span class="text-sm font-medium">{{
                            successMessage
                        }}</span>
                    </div>
                </Transition>

                <!-- PANEL PRINCIPAL -->
                <div
                    class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                >
                    <!-- Header del panel -->
                    <div class="flex items-center justify-between px-6 pt-6">
                        <h1 class="text-xl font-semibold text-slate-900">
                            Cursos registrados
                        </h1>

                        <button
                            @click="openModal"
                            class="inline-flex cursor-pointer items-center justify-center gap-1.5 rounded-lg bg-slate-900 px-3.5 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 active:bg-slate-950"
                        >
                            <Plus class="h-4 w-4" />
                            Añadir curso
                        </button>
                    </div>

                    <!-- Stat cards -->
                    <div
                        class="grid grid-cols-2 gap-3 px-6 pt-5 sm:grid-cols-5"
                    >
                        <div class="rounded-2xl bg-slate-50 px-4 py-3.5">
                            <p class="text-xs text-slate-500">Total cursos</p>
                            <p
                                class="mt-1 text-2xl font-semibold text-slate-900"
                            >
                                {{ stats.total }}
                            </p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3.5">
                            <p class="text-xs text-slate-500">En mañana</p>
                            <p
                                class="mt-1 text-2xl font-semibold text-slate-900"
                            >
                                {{ stats.manana }}
                            </p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3.5">
                            <p class="text-xs text-slate-500">En tarde</p>
                            <p
                                class="mt-1 text-2xl font-semibold text-slate-900"
                            >
                                {{ stats.tarde }}
                            </p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3.5">
                            <p class="text-xs text-slate-500">Profesores</p>
                            <p
                                class="mt-1 text-2xl font-semibold text-slate-900"
                            >
                                {{ stats.profesores }}
                            </p>
                        </div>
                        <div
                            class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3.5"
                        >
                            <p class="text-xs text-red-500">
                                Finalizan este mes
                            </p>
                            <p class="mt-1 text-2xl font-semibold text-red-700">
                                {{ stats.finalizanEsteMes }}
                            </p>
                        </div>
                    </div>

                    <!-- Buscador -->
                    <div class="px-6 pt-5">
                        <div class="relative">
                            <Search
                                class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-slate-400"
                            />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Buscar curso, profesor, aula..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pr-3 pl-9 text-sm text-slate-700 placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-900/10 focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- Lista de cursos -->
                    <div class="mt-4 divide-y divide-slate-100 px-3 pb-3">
                        <div
                            v-for="curso in filteredCursos"
                            :key="curso.id"
                            class="group relative flex items-center gap-4 rounded-2xl px-3 py-3.5 transition hover:bg-slate-50"
                        >
                            <!-- Icono de turno -->
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                                :class="
                                    curso.turno === 'manana'
                                        ? 'bg-amber-100 text-amber-600'
                                        : 'bg-violet-100 text-violet-600'
                                "
                            >
                                <span
                                    v-if="cursoFinalizado(curso.dia_final)"
                                    class="absolute top-3 right-40 rounded-full bg-red-600 px-2 py-1 text-[10px] font-semibold tracking-wide text-white uppercase shadow"
                                >
                                    Finalizado
                                </span>
                                <Sun
                                    v-if="curso.turno === 'manana'"
                                    class="h-5 w-5"
                                />
                                <Moon v-else class="h-5 w-5" />
                            </div>

                            <!-- Info principal -->
                            <div class="min-w-0 flex-1">
                                <Link
                                    :href="`/cursos/${curso.id}/detail`"
                                    class="font-semibold text-slate-900 hover:text-slate-700 hover:underline"
                                >
                                    {{ curso.nombre_curso }}
                                </Link>
                                <div
                                    class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500"
                                >
                                    <span
                                        class="inline-flex items-center gap-1"
                                    >
                                        <User class="h-3.5 w-3.5" />
                                        {{ curso.profesor }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1"
                                    >
                                        <MapPin class="h-3.5 w-3.5" />
                                        {{ curso.aula }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1"
                                    >
                                        <Calendar class="h-3.5 w-3.5" />
                                        {{ formatDate(curso.dia_comienzo) }} –
                                        {{ formatDate(curso.dia_final) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Chips -->
                            <div
                                class="hidden shrink-0 flex-col items-end gap-1.5 sm:flex"
                            >
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="
                                        curso.turno === 'manana'
                                            ? 'bg-amber-100 text-amber-700'
                                            : 'bg-violet-100 text-violet-700'
                                    "
                                >
                                    {{
                                        curso.turno === 'manana'
                                            ? 'Mañana'
                                            : 'Tarde'
                                    }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700"
                                    :title="curso.temario"
                                >
                                    <BookOpen class="h-3 w-3" />
                                    Materiales
                                </span>
                            </div>

                            <!-- Menú de acciones -->
                            <div class="relative shrink-0">
                                <button
                                    @click.stop="toggleMenu(curso.id)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <MoreHorizontal class="h-4 w-4" />
                                </button>

                                <Transition
                                    enter-active-class="transition duration-100 ease-out"
                                    enter-from-class="opacity-0 scale-95"
                                    enter-to-class="opacity-100 scale-100"
                                    leave-active-class="transition duration-75 ease-in"
                                    leave-from-class="opacity-100"
                                    leave-to-class="opacity-0"
                                >
                                    <div
                                        v-if="openMenuId === curso.id"
                                        @click.stop
                                        class="absolute right-0 z-10 mt-1.5 w-40 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg"
                                    >
                                        <button
                                            @click="
                                                editCurso(curso);
                                                closeMenu();
                                            "
                                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50"
                                        >
                                            <SquarePen class="h-3.5 w-3.5" />
                                            Editar
                                        </button>
                                        <button
                                            @click="
                                                destroy(curso.id);
                                                closeMenu();
                                            "
                                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                                        >
                                            <Trash2 class="h-3.5 w-3.5" />
                                            Eliminar
                                        </button>
                                    </div>
                                </Transition>
                            </div>
                        </div>

                        <!-- Estado vacío -->
                        <div
                            v-if="filteredCursos.length === 0"
                            class="flex flex-col items-center gap-2 py-16 text-center"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                            >
                                <GraduationCap class="h-6 w-6 text-slate-400" />
                            </div>
                            <p class="text-sm font-medium text-slate-600">
                                {{
                                    props.cursos.length === 0
                                        ? 'No hay cursos creados aún'
                                        : 'No se encontraron cursos'
                                }}
                            </p>
                            <p class="text-sm text-slate-400">
                                {{
                                    props.cursos.length === 0
                                        ? 'Añade tu primer curso para empezar'
                                        : 'Prueba con otro término de búsqueda'
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- COLUMNA DERECHA -->
            <div class="w-[560px] shrink-0">
                <!-- PANEL DE AULAS -->
                <div
                    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div class="mb-2">
                        <h2 class="text-lg font-semibold text-red-500">
                            Directo aulas
                        </h2>
                    </div>

                    <!-- ===================== PLANTA 1 ===================== -->

                    <h3
                        class="mb-5 text-center font-semibold tracking-wider uppercase"
                    >
                        Planta 1
                    </h3>

                    <div class="mb-5 flex justify-between">
                        <!-- Aula 6 -->
                        <div class="grid grid-cols-4 gap-2">
                            <div
                                v-for="aula in aulasPrimeraPlanta[0]"
                                :key="aula"
                                class="min-h-20 rounded-xl border p-2 text-center"
                                :class="
                                    cursoEnAula(aula)
                                        ? 'border-emerald-300 bg-emerald-50'
                                        : 'bg-slate-100'
                                "
                            >
                                <p class="font-bold">{{ aula }}</p>

                                <p
                                    v-if="cursoEnAula(aula)"
                                    class="mt-1 text-[10px] font-medium"
                                >
                                    {{ cursoEnAula(aula)?.nombre_curso }}
                                </p>

                                <p
                                    v-else
                                    class="mt-2 text-[10px] text-slate-400"
                                >
                                    Libre
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="my-8 border-t-4 border-dashed border-slate-300">
                        <p
                            class="mx-auto -mt-3 w-fit bg-white px-3 text-center text-xs text-slate-500"
                        >
                            PASILLO
                        </p>
                    </div>

                    <div class="flex items-center justify-around">
                        <!-- AT 07 -->
                        <div class="grid grid-cols-3 gap-2">
                            <div
                                v-for="aula in aulasPrimeraPlanta[1]"
                                :key="aula"
                                class="min-h-20 min-w-[150px] rounded-xl border p-2 text-center"
                                :class="
                                    cursoEnAula(aula)
                                        ? 'border-emerald-300 bg-emerald-50'
                                        : 'bg-slate-100'
                                "
                            >
                                <p class="font-bold">{{ aula }}</p>

                                <p
                                    v-if="cursoEnAula(aula)"
                                    class="mt-1 text-[10px] font-medium"
                                >
                                    {{ cursoEnAula(aula)?.nombre_curso }}
                                </p>

                                <p
                                    v-else
                                    class="mt-2 text-[10px] text-slate-400"
                                >
                                    Libre
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-10" />

                    <!-- ===================== PLANTA 2 ===================== -->

                    <h3
                        class="mb-5 text-center font-semibold tracking-wider uppercase"
                    >
                        Planta 2
                    </h3>

                    <div class="mb-8 flex justify-between">
                        <div class="grid grid-cols-4 gap-2">
                            <div
                                v-for="aula in aulasSegundaPlanta[0]"
                                :key="aula"
                                class="min-h-20 rounded-xl border p-2 text-center"
                                :class="
                                    cursoEnAula(aula)
                                        ? 'border-emerald-300 bg-emerald-50'
                                        : 'bg-slate-100'
                                "
                            >
                                <p class="font-bold">{{ aula }}</p>

                                <p
                                    v-if="cursoEnAula(aula)"
                                    class="mt-1 text-[10px] font-medium"
                                >
                                    {{ cursoEnAula(aula)?.nombre_curso }}
                                </p>

                                <p
                                    v-else
                                    class="mt-2 text-[10px] text-slate-400"
                                >
                                    Libre
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="my-8 border-t-4 border-dashed border-slate-300">
                        <p
                            class="mx-auto -mt-3 w-fit bg-white px-3 text-center text-xs text-slate-500"
                        >
                            PASILLO
                        </p>
                    </div>

                    <div class="flex items-center justify-around">
                        <div class="grid grid-cols-3 gap-2">
                            <div
                                v-for="aula in aulasSegundaPlanta[1]"
                                :key="aula"
                                class="flex min-h-[90px] min-w-[150px] flex-col items-center justify-center rounded-xl border p-2 text-center"
                                :class="
                                    cursoEnAula(aula)
                                        ? 'border-emerald-300 bg-emerald-50'
                                        : 'bg-slate-100'
                                "
                            >
                                <p class="font-bold">{{ aula }}</p>

                                <p
                                    v-if="cursoEnAula(aula)"
                                    class="mt-1 text-[10px] font-medium"
                                >
                                    {{ cursoEnAula(aula)?.nombre_curso }}
                                </p>

                                <p
                                    v-else
                                    class="mt-2 text-[10px] text-slate-400"
                                >
                                    Libre
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                @click.stop
            >
                <!-- overlay -->
                <div
                    class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                    @click="closeModal"
                ></div>

                <!-- modal box -->
                <Transition
                    appear
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                >
                    <div
                        class="relative z-10 w-full max-w-xl rounded-2xl bg-white shadow-xl"
                    >
                        <!-- header -->
                        <div
                            class="flex items-center justify-between border-b border-slate-100 px-6 py-4"
                        >
                            <div>
                                <h2
                                    class="text-lg font-semibold text-slate-900"
                                >
                                    {{
                                        isEditing
                                            ? 'Editar curso'
                                            : 'Crear curso'
                                    }}
                                </h2>
                                <p class="text-sm text-slate-500">
                                    {{
                                        isEditing
                                            ? 'Actualiza los datos del curso seleccionado'
                                            : 'Rellena los datos para registrar un nuevo curso'
                                    }}
                                </p>
                            </div>

                            <button
                                @click="closeModal"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                            >
                                <X class="h-4 w-4" />
                            </button>
                        </div>

                        <!-- form -->
                        <form
                            @submit.prevent="submit"
                            class="grid gap-4 px-6 py-5"
                        >
                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-medium text-slate-600"
                                    >Nombre del curso</label
                                >
                                <input
                                    v-model="form.nombre_curso"
                                    placeholder="Ej. Desarrollo Web Full Stack"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10 focus:outline-none"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-1.5">
                                    <label
                                        class="text-xs font-medium text-slate-600"
                                        >Profesor</label
                                    >
                                    <input
                                        v-model="form.profesor"
                                        placeholder="Nombre del profesor"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10 focus:outline-none"
                                    />
                                </div>
                                <div class="grid gap-1.5">
                                    <label
                                        class="text-xs font-medium text-slate-600"
                                        >Email</label
                                    >
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        placeholder="email@gmail.com"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10 focus:outline-none"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-1.5">
                                    <label
                                        class="text-xs font-medium text-slate-600"
                                        >Aula</label
                                    >
                                    <input
                                        v-model="form.aula"
                                        placeholder="Ej. A-204"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10 focus:outline-none"
                                    />
                                </div>
                                <div class="grid gap-1.5">
                                    <label
                                        class="text-xs font-medium text-slate-600"
                                        >Turno</label
                                    >
                                    <select
                                        v-model="form.turno"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10 focus:outline-none"
                                    >
                                        <option value="manana">Mañana</option>
                                        <option value="tarde">Tarde</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-medium text-slate-600"
                                    >Temario</label
                                >
                                <input
                                    v-model="form.temario"
                                    placeholder="Contenidos principales del curso"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10 focus:outline-none"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-1.5">
                                    <label
                                        class="text-xs font-medium text-slate-600"
                                        >Fecha de inicio</label
                                    >
                                    <input
                                        type="date"
                                        v-model="form.dia_comienzo"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10 focus:outline-none"
                                    />
                                </div>
                                <div class="grid gap-1.5">
                                    <label
                                        class="text-xs font-medium text-slate-600"
                                        >Fecha de fin</label
                                    >
                                    <input
                                        type="date"
                                        v-model="form.dia_final"
                                        class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10 focus:outline-none"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-1.5">
                                <label
                                    class="text-xs font-medium text-slate-600"
                                    >Técnico</label
                                >
                                <!-- Ver si se puede cambiar a un select -->
                                <input
                                    v-model="form.tecnico"
                                    placeholder="Nombre del técnico responsable"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10 focus:outline-none"
                                />
                            </div>

                            <div
                                class="mt-2 flex justify-end gap-2 border-t border-slate-100 pt-4"
                            >
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                                >
                                    Cancelar
                                </button>

                                <button
                                    :disabled="form.processing"
                                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    {{
                                        isEditing
                                            ? 'Guardar cambios'
                                            : 'Crear curso'
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>
    </div>
</template>
