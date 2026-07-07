<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';

const props = defineProps({
    cursos: Array,
});

const events = (props.cursos ?? []).map((curso: any) => ({
    title: curso.nombre,
    start: curso.dia_comienzo,
    end: curso.dia_final,
}));

const calendarOptions = {
    plugins: [dayGridPlugin],
    initialView: 'dayGridMonth',
    events,
    locale: 'es',
    firstDay: 1,
};
</script>

<template>
    <Head title="Calendario" />

    <div class="p-6">
        <FullCalendar :options="calendarOptions" />
    </div>
</template>
