<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    FolderGit2,
    LayoutGrid,
    Book,
    CalendarDays,
    ClipboardCheck,
    Users,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
//en estos imports de dashboard, cursos, calendario etc, solo funciona con el comando php artisan wayfinder:generate,
//y si existe en web.php algo como esto en el name Route::get('/alumnos',[AlumnoController::class,'index'])->name('alumnos');
//tiene que haber un name->(alumnos) para que renderice bien, sino da error
import {
    dashboard,
    cursos,
    calendario,
    users,
    alumnos,
    faltas,
} from '@/routes';
import type { NavItem } from '@/types';

//Obtener el usuario, para asignarle permisos
const page = usePage();
const user = page.props.auth.user as any;

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Cursos',
        href: cursos(),
        icon: Book,
    },
    {
        title: 'Calendario',
        href: calendario(),
        icon: CalendarDays,
    },
    {
        title: 'Alumnos',
        href: alumnos(),
        icon: Users,
    },
    {
        title: 'Faltas',
        href: faltas(),
        icon: ClipboardCheck,
    },
    ...(user.role !== 'coordinator'
        ? [
              {
                  title: 'Usuarios',
                  href: users(),
                  icon: CalendarDays,
              },
          ]
        : []),
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
