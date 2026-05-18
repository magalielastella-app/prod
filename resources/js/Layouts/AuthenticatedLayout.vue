<script setup>
import { ref, computed } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import FlashToast from '@/Components/FlashToast.vue';
import BrandLogo from '@/Components/BrandLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
const isAdmin = computed(() => page.props.auth.user?.role === 'admin');
</script>

<template>
    <div>
        <div class="min-h-screen">
            <!-- Barre de navigation -->
            <nav class="sticky top-0 z-20 border-b border-brand-beige/70 bg-white/80 backdrop-blur-md shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')"
                                    class="flex items-center gap-3 transition hover:opacity-90"
                                    aria-label="Cabinet Dentaire de l'Obiou — Tableau de bord">
                                    <BrandLogo variant="mark" size="sm" />
                                    <span class="hidden sm:block">
                                        <span class="block text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-brand-cream">Cabinet Dentaire</span>
                                        <span class="block text-[10px] uppercase tracking-[0.2em] text-brand-primary">de l'Obiou</span>
                                    </span>
                                </Link>
                            </div>

                            <!-- Liens navigation -->
                            <div class="hidden space-x-5 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Tableau de bord</NavLink>
                                <NavLink :href="route('reviews.index')" :active="route().current('reviews.*')">Entretiens</NavLink>
                                <NavLink v-if="isAdmin" :href="route('templates.index')" :active="route().current('templates.*')">Trames</NavLink>
                                <NavLink v-if="isAdmin" :href="route('team.index')" :active="route().current('team.*')">Équipe</NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-600 transition hover:text-brand-primary focus:outline-none dark:bg-gray-800 dark:text-gray-300">
                                            {{ page.props.auth.user.name }}
                                            <svg class="-me-0.5 ms-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">Profil</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">Déconnexion</DropdownLink>
                                </template>
                            </Dropdown>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-brand-primary hover:bg-brand-tertiary focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Menu mobile -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Tableau de bord</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('reviews.index')" :active="route().current('reviews.*')">Entretiens</ResponsiveNavLink>
                        <ResponsiveNavLink v-if="isAdmin" :href="route('templates.index')" :active="route().current('templates.*')">Trames</ResponsiveNavLink>
                        <ResponsiveNavLink v-if="isAdmin" :href="route('team.index')" :active="route().current('team.*')">Équipe</ResponsiveNavLink>
                    </div>
                    <div class="border-t border-brand-tan/50 pb-1 pt-4 dark:border-gray-600">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ page.props.auth.user.name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ page.props.auth.user.email }}</div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Profil</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Déconnexion</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <header v-if="$slots.header" class="border-b border-brand-beige/50 bg-white/70 shadow-sm backdrop-blur dark:border-gray-700 dark:bg-gray-800/70">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
        <FlashToast />
    </div>
</template>
