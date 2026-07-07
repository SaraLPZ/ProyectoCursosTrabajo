<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showModal = ref(false);

function openModal() {
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
}

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'coordinator',
});

function submit() {
    form.post('/users', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            closeModal();
        },
    });
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: '/users',
            },
        ],
    },
});

interface User {
    id: number;
    name: string;
    email: string;
    role: 'super_admin' | 'admin' | 'coordinator';
}

defineProps<{
    users: User[];
}>();
</script>

<template>
    <Head title="Usuarios" />

    <div class="p-6">
        <button
            @click="openModal"
            class="mb-4 rounded-xl bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
        >
            + Crear usuario
        </button>
        <h1 class="mb-4 text-2xl font-bold">Usuarios</h1>

        <div class="rounded-xl border bg-white shadow-sm">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2 text-left">ID</th>
                        <th class="border p-2 text-left">Nombre</th>
                        <th class="border p-2 text-left">Email</th>
                        <th class="border p-2 text-left">Rol</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="user in users"
                        :key="user.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="border p-2">{{ user.id }}</td>
                        <td class="border p-2">{{ user.name }}</td>
                        <td class="border p-2">{{ user.email }}</td>
                        <!-- role -->
                        <td class="border p-2">
                            <span
                                class="rounded-full px-2 py-1 text-xs font-medium"
                                :class="{
                                    'bg-red-100 text-red-700':
                                        user.role === 'super_admin',
                                    'bg-blue-100 text-blue-700':
                                        user.role === 'admin',
                                    'bg-green-100 text-green-700':
                                        user.role === 'coordinator',
                                }"
                            >
                                {{ user.role }}
                            </span>
                        </td>
                    </tr>

                    <tr v-if="users.length === 0">
                        <td colspan="4" class="p-6 text-center text-gray-500">
                            No hay usuarios registrados
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div
        v-if="showModal"
        class="fixed inset-0 flex items-center justify-center bg-black/50"
    >
        <div class="w-full max-w-md rounded-xl bg-white p-6">
            <h2 class="mb-4 text-xl font-bold">Crear usuario</h2>

            <form @submit.prevent="submit" class="space-y-3">
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Nombre"
                    class="w-full rounded border p-2"
                />

                <input
                    v-model="form.email"
                    type="email"
                    placeholder="Email"
                    class="w-full rounded border p-2"
                />

                <input
                    v-model="form.password"
                    type="password"
                    placeholder="Password"
                    class="w-full rounded border p-2"
                />

                <select v-model="form.role" class="w-full rounded border p-2">
                    <option value="super_admin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="coordinator">Coordinator</option>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeModal">Cancelar</button>

                    <button
                        type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white"
                    >
                        Crear
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
