<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

export default {
    components: { AppLayout },
    layout: AppLayout,
    props: {
        targets: Array,
        currentWeekStart: String,
    },
    setup(props) {
        const form = useForm({
            week_start_date: props.currentWeekStart,
            target_calories: '',
        });
        return { form };
    },
    methods: {
        submit() {
            this.form.post('/objectifs', { preserveScroll: true });
        },
        remove(id) {
            if (confirm('Supprimer cet objectif ?')) {
                this.$inertia.delete(`/objectifs/${id}`, { preserveScroll: true });
            }
        },
    },
};
</script>

<template>
    <div class="mx-auto max-w-xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Objectifs caloriques</h1>
            <p class="mt-1 text-sm text-slate-500">Défini par semaine (lundi au dimanche).</p>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form @submit.prevent="submit" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="mb-1 block text-xs text-slate-500">Semaine du</label>
                    <input v-model="form.week_start_date" type="date" class="rounded-lg border-slate-300 text-sm" />
                </div>
                <div>
                    <label class="mb-1 block text-xs text-slate-500">Objectif (kcal/jour)</label>
                    <input v-model="form.target_calories" type="number" required class="rounded-lg border-slate-300 text-sm" />
                </div>
                <button type="submit" :disabled="form.processing"
                    class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-medium text-white hover:bg-sky-600">
                    Enregistrer
                </button>
            </form>
        </section>

        <section class="divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div v-for="t in targets" :key="t.id" class="flex items-center justify-between px-5 py-3">
                <div>
                    <div class="text-sm font-medium text-slate-700">Semaine du {{ t.week_start_date }}</div>
                    <div class="text-xs text-slate-400">{{ t.target_calories }} kcal / jour</div>
                </div>
                <button @click="remove(t.id)" class="text-xs text-red-500 hover:underline">Supprimer</button>
            </div>
            <p v-if="!targets.length" class="px-5 py-6 text-center text-sm text-slate-400">Aucun objectif défini.</p>
        </section>
    </div>
</template>
