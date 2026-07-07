<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { dashboard } from '@/routes';

//Detectar el usuario para darle permisos de vista
const page = usePage();
const user = page.props.auth.user;

//condicion mas limpia con variable y usarla en la vista directamente
const isCoordinator = user.role === 'coordinator';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <div class="mx-auto max-w-6xl space-y-8 p-6">
        <!-- HEADER -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Gestión de usuarios
                </h1>
                <p class="text-sm text-gray-500">
                    Administra usuarios, roles y permisos del sistema
                </p>
            </div>

            <button
                class="rounded-xl bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700"
            >
                + Nuevo usuario
            </button>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-2xl border bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total usuarios</p>
                <p class="text-2xl font-bold">128</p>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Administradores</p>
                <p class="text-2xl font-bold">5</p>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Activos hoy</p>
                <p class="text-2xl font-bold">42</p>
            </div>
        </div>

        <!-- TABLE -->
        <div
            v-if="!isCoordinator"
            class="rounded-2xl border bg-white shadow-sm"
        >
            <!-- TABLE HEADER -->
            <div class="flex items-center justify-between border-b p-5">
                <h2 class="text-lg font-semibold text-gray-800">
                    Lista de usuarios
                </h2>

                <input
                    type="text"
                    placeholder="Buscar usuario..."
                    class="w-64 rounded-lg border px-3 py-2 text-sm"
                />
            </div>

            <!-- TABLE -->
            <div class="w-full overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-3 text-left">Usuario</th>
                            <th class="px-5 py-3 text-left">Email</th>
                            <th class="px-5 py-3 text-left">Rol</th>
                            <th class="px-5 py-3 text-left">Estado</th>
                            <th class="px-5 py-3 text-left">Último acceso</th>
                            <th class="px-5 py-3 text-left">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        <!-- ROW -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 font-medium text-gray-900">
                                Nombre Usuario
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                usuario@email.com
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="rounded-full bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700"
                                >
                                    Admin
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700"
                                >
                                    Activo
                                </span>
                            </td>

                            <td class="px-5 py-4 text-gray-500">
                                Hace 2 horas
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex gap-2">
                                    <button
                                        class="rounded-lg bg-blue-50 px-3 py-1 text-sm text-blue-600 hover:bg-blue-100"
                                    >
                                        Editar
                                    </button>

                                    <button
                                        class="rounded-lg bg-red-50 px-3 py-1 text-sm text-red-600 hover:bg-red-100"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL (solo UI) -->
    <!-- <div class="fixed inset-0 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Crear usuario</h2>

                <button class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <div class="grid gap-3">
                <input
                    type="text"
                    placeholder="Nombre"
                    class="rounded-xl border p-2"
                />

                <input
                    type="email"
                    placeholder="Email"
                    class="rounded-xl border p-2"
                />

                <select class="rounded-xl border p-2">
                    <option>Admin</option>
                    <option>Profesor</option>
                    <option>Alumno</option>
                </select>

                <div class="flex justify-end gap-2 pt-2">
                    <button class="rounded-xl border px-4 py-2">
                        Cancelar
                    </button>

                    <button
                        class="rounded-xl bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div> -->
    <!-- PANEL LATERAL -->
    <div class="h-fit lg:sticky lg:top-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900">
                    Aulas en directo
                </h2>

                <p class="text-sm text-slate-500">Estado actual del centro</p>
            </div>

            <!-- Primera planta -->

            <div class="mb-3">
                <div
                    class="mb-2 text-center text-xs font-semibold tracking-widest text-slate-500 uppercase"
                >
                    Primera planta
                </div>

                <div class="grid grid-cols-5 gap-2">
                    <div
                        class="rounded-lg border border-emerald-300 bg-emerald-100 p-2 text-center"
                    >
                        <div class="text-xs font-semibold">A1</div>
                        <div class="mt-1 text-[10px] leading-tight">
                            Laravel
                        </div>
                    </div>

                    <div class="rounded-lg border bg-slate-100 p-2 text-center">
                        <div class="text-xs font-semibold">A2</div>
                        <div class="mt-2 text-[10px] text-slate-400">Libre</div>
                    </div>

                    <div
                        class="rounded-lg border border-emerald-300 bg-emerald-100 p-2 text-center"
                    >
                        <div class="text-xs font-semibold">A3</div>
                        <div class="mt-1 text-[10px]">Unity</div>
                    </div>

                    <div class="rounded-lg border bg-slate-100 p-2 text-center">
                        <div class="text-xs font-semibold">A4</div>
                        <div class="mt-2 text-[10px] text-slate-400">Libre</div>
                    </div>

                    <div
                        class="rounded-lg border border-amber-300 bg-amber-100 p-2 text-center"
                    >
                        <div class="text-xs font-semibold">A5</div>
                        <div class="mt-1 text-[10px]">15:00</div>
                    </div>
                </div>
            </div>

            <div
                class="my-5 rounded-full bg-slate-200 py-1 text-center text-[10px] font-bold tracking-widest text-slate-500"
            >
                PASILLO
            </div>

            <!-- Segunda planta -->

            <div>
                <div
                    class="mb-2 text-center text-xs font-semibold tracking-widest text-slate-500 uppercase"
                >
                    Segunda planta
                </div>

                <div class="grid grid-cols-5 gap-2">
                    <div class="rounded-lg border bg-slate-100 p-2 text-center">
                        <div class="text-xs font-semibold">B1</div>
                        <div class="mt-2 text-[10px] text-slate-400">Libre</div>
                    </div>

                    <div
                        class="rounded-lg border border-emerald-300 bg-emerald-100 p-2 text-center"
                    >
                        <div class="text-xs font-semibold">B2</div>
                        <div class="mt-1 text-[10px]">Redes</div>
                    </div>

                    <div
                        class="rounded-lg border border-emerald-300 bg-emerald-100 p-2 text-center"
                    >
                        <div class="text-xs font-semibold">B3</div>
                        <div class="mt-1 text-[10px]">Unreal</div>
                    </div>

                    <div class="rounded-lg border bg-slate-100 p-2 text-center">
                        <div class="text-xs font-semibold">B4</div>
                        <div class="mt-2 text-[10px] text-slate-400">Libre</div>
                    </div>

                    <div class="rounded-lg border bg-slate-100 p-2 text-center">
                        <div class="text-xs font-semibold">B5</div>
                        <div class="mt-2 text-[10px] text-slate-400">Libre</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
