<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import FlashToast from '@/Components/FlashToast.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
</script>

<template>
    <div>
        <div class="min-h-screen bg-brand-cream dark:bg-[#1C1512]">
            <!-- Barre de navigation -->
            <nav class="border-b border-brand-tan/50 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo Smash You -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')" class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 place-items-center rounded-full bg-brand-primary text-lg font-black text-brand-cream shadow">
                                        SY
                                    </span>
                                    <span class="hidden sm:block">
                                        <span class="block text-base font-bold tracking-wide text-brand-primary dark:text-brand-cream">Smash You</span>
                                        <span class="block text-[11px] uppercase tracking-wider text-brand-tan">Management</span>
                                    </span>
                                </Link>
                            </div>

                            <!-- Liens navigation -->
                            <div class="hidden space-x-5 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Tableau de bord</NavLink>
                                <NavLink :href="route('products.index')" :active="route().current('products.*') || route().current('stock.*')">Inventaire</NavLink>
                                <NavLink :href="route('suppliers.index')" :active="route().current('suppliers.*') || route().current('cadencier.*') || route().current('invoices.*')">Achat</NavLink>
                                <NavLink :href="route('planning.index')" :active="route().current('planning.*') || route().current('employees.*') || route().current('shifts.*')">Planning</NavLink>
                                <NavLink :href="route('hygiene.index')" :active="route().current('hygiene.*') || route().current('temperatures.*') || route().current('cleaning-tasks.*') || route().current('deliveries.*')">Hygiène</NavLink>
                                <NavLink :href="route('cash.index')" :active="route().current('cash.*')">Caisse</NavLink>
                                <NavLink :href="route('reviews.index')" :active="route().current('reviews.*')">Entretiens</NavLink>
                                <NavLink :href="route('tools.index')" :active="route().current('tools.*') || route().current('documents.*') || route().current('company.*')">Outils</NavLink>
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
                        <ResponsiveNavLink :href="route('products.index')" :active="route().current('products.*')">Inventaire</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('suppliers.index')" :active="route().current('suppliers.*') || route().current('invoices.*')">Achat</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('planning.index')" :active="route().current('planning.*')">Planning</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('hygiene.index')" :active="route().current('hygiene.*')">Hygiène</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('cash.index')" :active="route().current('cash.*')">Caisse</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('reviews.index')" :active="route().current('reviews.*')">Entretiens</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('tools.index')" :active="route().current('tools.*')">Outils</ResponsiveNavLink>
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

            <header v-if="$slots.header" class="border-b border-brand-tan/30 bg-white/80 shadow-sm backdrop-blur dark:border-gray-700 dark:bg-gray-800">
                <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
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
