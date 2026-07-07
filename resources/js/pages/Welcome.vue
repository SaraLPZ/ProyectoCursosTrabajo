<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';
import { register } from '@/routes';
import welcome from '@/assets/images/welcome.jpg';
</script>

<template>
    <Head title="Centro de Formación Digital">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a]"
    >
        <!-- HEADER / NAV -->
        <header class="w-full border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            <nav
                class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4"
            >
                <div class="text-lg font-bold">FormaDigital</div>

                <div
                    class="hidden gap-6 text-sm text-[#706f6c] sm:flex dark:text-[#A1A09A]"
                >
                    <a href="#inicio">Inicio</a>
                    <a href="#servicios">Qué hacemos</a>
                    <a href="#nosotros">Sobre nosotros</a>
                    <a href="#contacto">Contacto</a>
                </div>

                <div class="flex items-center gap-4 text-sm">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="register()"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-6">
            <!-- HERO -->
            <section
                id="inicio"
                class="grid grid-cols-1 items-center gap-10 py-16 lg:grid-cols-2 lg:py-24"
            >
                <div>
                    <h1 class="mb-4 text-4xl font-bold lg:text-5xl">
                        Formación digital que impulsa tu futuro
                    </h1>
                    <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">
                        Cursos online y presenciales para adquirir las
                        habilidades digitales más demandadas por las empresas.
                    </p>
                    <div class="flex gap-4">
                        <a
                            href="#servicios"
                            class="inline-block rounded-sm border border-black bg-[#1b1b18] px-6 py-2.5 text-sm font-medium text-white hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white"
                        >
                            Ver cursos
                        </a>
                        <Link
                            :href="register()"
                            class="inline-block rounded-sm border border-[#19140035] px-6 py-2.5 text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Crear cuenta
                        </Link>
                    </div>
                </div>

                <div
                    class="overflow-hidden rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <img
                        :src="welcome"
                        alt="Alumnos en una clase del centro de formación"
                        class="h-full w-full object-cover"
                    />
                </div>
            </section>

            <!-- ESTADÍSTICAS -->
            <section class="mb-16 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div
                    class="rounded-lg bg-white p-6 text-center shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <div class="text-3xl font-bold">+1.200</div>
                    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        Alumnos formados
                    </div>
                </div>
                <div
                    class="rounded-lg bg-white p-6 text-center shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <div class="text-3xl font-bold">+30</div>
                    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        Cursos activos
                    </div>
                </div>
                <div
                    class="rounded-lg bg-white p-6 text-center shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <div class="text-3xl font-bold">10 años</div>
                    <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        de experiencia
                    </div>
                </div>
            </section>

            <!-- QUÉ HACEMOS -->
            <section id="servicios" class="mb-16">
                <h2 class="mb-6 text-2xl font-bold">Qué hacemos</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div
                        class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                    >
                        <h3 class="mb-2 font-medium">Formación práctica</h3>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Cursos impartidos por profesionales del sector con
                            metodología orientada a proyectos reales.
                        </p>
                    </div>
                    <div
                        class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                    >
                        <h3 class="mb-2 font-medium">Certificación oficial</h3>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Acreditamos cada curso con certificados reconocidos
                            por el sector digital.
                        </p>
                    </div>
                    <div
                        class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                    >
                        <h3 class="mb-2 font-medium">Acompañamiento</h3>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Coordinadores que siguen el progreso de cada alumno
                            durante todo el curso.
                        </p>
                    </div>
                </div>
            </section>

            <!-- SOBRE NOSOTROS -->
            <section
                id="nosotros"
                class="mb-16 rounded-lg bg-white p-8 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
            >
                <h2 class="mb-4 text-2xl font-bold">Sobre nosotros</h2>
                <p class="max-w-3xl text-[#706f6c] dark:text-[#A1A09A]">
                    Somos un centro de formación digital comprometido con la
                    calidad educativa. Contamos con un equipo de coordinadores y
                    formadores especializados que acompañan a cada alumno en su
                    proceso de aprendizaje, con metodologías actualizadas y
                    adaptadas a las necesidades del mercado laboral actual.
                </p>
            </section>

            <!-- CONTACTO -->
            <section id="contacto" class="mb-16">
                <h2 class="mb-4 text-2xl font-bold">Contacto</h2>
                <div
                    class="flex flex-wrap gap-6 text-sm text-[#706f6c] dark:text-[#A1A09A]"
                >
                    <span>info@formadigital.test</span>
                    <span>900 000 000</span>
                    <span>Madrid, España</span>
                </div>
            </section>
        </main>

        <!-- FOOTER -->
        <footer class="border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
            <div
                class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-6 py-6 text-sm text-[#706f6c] sm:flex-row dark:text-[#A1A09A]"
            >
                <span>© 2026 FormaDigital. Todos los derechos reservados.</span>
                <span>Aviso legal · Privacidad · Cookies</span>
            </div>
        </footer>
    </div>
</template>
