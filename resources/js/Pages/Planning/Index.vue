<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataModal from '@/Components/DataModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    employees: { type: Array, required: true },
    shifts: { type: Array, required: true },
    weekStart: { type: String, required: true },
});

const DAYS_FR = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

function addDays(dateStr, n) {
    const d = new Date(dateStr + 'T00:00:00');
    d.setDate(d.getDate() + n);
    return d;
}
function iso(d) {
    return d.toISOString().slice(0, 10);
}
function formatFr(d) {
    return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
}

// Dates de la semaine affichée
const weekDates = computed(() => Array.from({ length: 7 }, (_, i) => addDays(props.weekStart, i)));
const weekLabel = computed(() => {
    const start = addDays(props.weekStart, 0);
    const end = addDays(props.weekStart, 6);
    return `${start.toLocaleDateString('fr-FR')} — ${end.toLocaleDateString('fr-FR')}`;
});

function shiftsFor(employeeId, date) {
    const iso_ = iso(date);
    return props.shifts.filter((s) => s.employee_id === employeeId && s.date === iso_);
}

function navigate(offsetDays) {
    router.get(route('planning.index'),
        { week: iso(addDays(props.weekStart, offsetDays)) },
        { preserveState: false, replace: true }
    );
}

// Modales
const showEmpModal = ref(false);
const empForm = useForm({ name: '', role: '', email: '', phone: '' });
function submitEmployee() {
    empForm.post(route('employees.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showEmpModal.value = false;
            empForm.reset();
        },
    });
}
function deleteEmployee(emp) {
    if (!confirm(`Supprimer ${emp.name} ? Tous ses créneaux seront également supprimés.`)) return;
    router.delete(route('employees.destroy', emp.id), { preserveScroll: true });
}

const showShiftModal = ref(false);
const shiftForm = useForm({ employee_id: '', date: iso(new Date()), start: '', end: '', role: '' });
function openShiftModal() {
    if (!props.employees.length) {
        alert('Ajoutez d\'abord un employé');
        return;
    }
    shiftForm.clearErrors();
    shiftForm.employee_id = props.employees[0].id;
    showShiftModal.value = true;
}
function submitShift() {
    shiftForm.post(route('shifts.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showShiftModal.value = false;
            shiftForm.reset();
            shiftForm.date = iso(new Date());
        },
    });
}
function deleteShift(s) {
    router.delete(route('shifts.destroy', s.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Planning" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Planning hebdomadaire
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                    <SecondaryButton @click="navigate(-7)">← Sem. précédente</SecondaryButton>
                    <span class="min-w-48 text-center font-semibold text-gray-800 dark:text-gray-100">{{ weekLabel }}</span>
                    <SecondaryButton @click="navigate(7)">Sem. suivante →</SecondaryButton>
                    <div class="ml-auto flex gap-2">
                        <PrimaryButton @click="showEmpModal = true">+ Employé</PrimaryButton>
                        <PrimaryButton @click="openShiftModal">+ Créneau</PrimaryButton>
                    </div>
                </div>

                <!-- Équipe -->
                <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                    <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Équipe</h3>
                    <div v-if="!employees.length" class="text-sm italic text-gray-500">
                        Aucun employé. Ajoutez votre équipe pour créer des créneaux.
                    </div>
                    <div v-else class="flex flex-wrap gap-2">
                        <span v-for="e in employees" :key="e.id"
                            class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-sm dark:bg-gray-700 dark:text-gray-200">
                            <strong>{{ e.name }}</strong>
                            <span class="text-gray-500">{{ e.role || '' }}</span>
                            <button @click="deleteEmployee(e)" class="text-red-500 hover:text-red-700" title="Supprimer">×</button>
                        </span>
                    </div>
                </div>

                <!-- Grille planning -->
                <div class="overflow-x-auto rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <table class="min-w-full border-collapse text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
                                <th class="border border-gray-200 p-2 text-left text-xs font-semibold uppercase text-gray-500 dark:border-gray-700">
                                    Employé
                                </th>
                                <th v-for="(d, i) in weekDates" :key="i"
                                    class="border border-gray-200 p-2 text-center text-xs font-semibold uppercase text-gray-500 dark:border-gray-700">
                                    {{ DAYS_FR[i] }}<br>
                                    <span class="text-gray-400">{{ formatFr(d) }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!employees.length">
                                <td :colspan="8" class="p-8 text-center italic text-gray-500">
                                    Ajoutez au moins un employé pour générer le planning.
                                </td>
                            </tr>
                            <tr v-for="emp in employees" :key="emp.id">
                                <td class="border border-gray-200 bg-gray-50 p-2 font-semibold dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                    {{ emp.name }}
                                </td>
                                <td v-for="(d, i) in weekDates" :key="i"
                                    class="border border-gray-200 p-1 align-top dark:border-gray-700" style="min-width: 110px;">
                                    <div v-for="s in shiftsFor(emp.id, d)" :key="s.id"
                                        class="relative mb-1 rounded bg-indigo-600 px-2 py-1 text-xs text-white">
                                        {{ s.start }}–{{ s.end }}
                                        <div v-if="s.role" class="text-[11px] opacity-90">{{ s.role }}</div>
                                        <button @click="deleteShift(s)"
                                            class="absolute right-1 top-0.5 text-white/70 hover:text-white"
                                            title="Supprimer">×</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal employé -->
        <DataModal :show="showEmpModal" title="Nouvel employé" @close="showEmpModal = false">
            <form @submit.prevent="submitEmployee" class="space-y-4">
                <div>
                    <InputLabel value="Nom complet *" />
                    <TextInput v-model="empForm.name" required class="mt-1 block w-full" />
                    <InputError :message="empForm.errors.name" class="mt-1" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Poste" />
                        <TextInput v-model="empForm.role" placeholder="Chef, Serveur..." class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Téléphone" />
                        <TextInput v-model="empForm.phone" class="mt-1 block w-full" />
                    </div>
                </div>
                <div>
                    <InputLabel value="Email" />
                    <TextInput v-model="empForm.email" type="email" class="mt-1 block w-full" />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <SecondaryButton @click="showEmpModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="empForm.processing">Enregistrer</PrimaryButton>
                </div>
            </form>
        </DataModal>

        <!-- Modal créneau -->
        <DataModal :show="showShiftModal" title="Nouveau créneau" @close="showShiftModal = false">
            <form @submit.prevent="submitShift" class="space-y-4">
                <div>
                    <InputLabel value="Employé *" />
                    <select v-model="shiftForm.employee_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        <option v-for="e in employees" :key="e.id" :value="e.id">
                            {{ e.name }} ({{ e.role || '—' }})
                        </option>
                    </select>
                    <InputError :message="shiftForm.errors.employee_id" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Date *" />
                    <TextInput v-model="shiftForm.date" type="date" required class="mt-1 block w-full" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Début *" />
                        <TextInput v-model="shiftForm.start" type="time" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel value="Fin *" />
                        <TextInput v-model="shiftForm.end" type="time" required class="mt-1 block w-full" />
                        <InputError :message="shiftForm.errors.end" class="mt-1" />
                    </div>
                </div>
                <div>
                    <InputLabel value="Poste/service" />
                    <TextInput v-model="shiftForm.role" placeholder="Service midi, soir..." class="mt-1 block w-full" />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <SecondaryButton @click="showShiftModal = false">Annuler</SecondaryButton>
                    <PrimaryButton :disabled="shiftForm.processing">Enregistrer</PrimaryButton>
                </div>
            </form>
        </DataModal>
    </AuthenticatedLayout>
</template>
