<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

export default {
    components: { AppLayout },
    layout: AppLayout,
    props: {
        meals: Array,
        totalPortions: Number,
        totalCalories: Number,
    },
    data() {
        return {
            editingId: null,
        };
    },
    setup() {
        const form = useForm({ name: '', calories_per_portion: '', portions: '' });
        const editForm = useForm({ name: '', calories_per_portion: '', portions: '' });
        return { form, editForm };
    },
    methods: {
        submit() {
            this.form.post('/stock', {
                preserveScroll: true,
                onSuccess: () => this.form.reset(),
            });
        },
        startEdit(meal) {
            this.editingId = meal.id;
            this.editForm.name = meal.name;
            this.editForm.calories_per_portion = meal.calories_per_portion;
            this.editForm.portions = meal.portions;
        },
        saveEdit(id) {
            this.editForm.put(`/stock/${id}`, {
                preserveScroll: true,
                onSuccess: () => (this.editingId = null),
            });
        },
        remove(id) {
            if (confirm('Supprimer ce plat du stock ?')) {
                this.$inertia.delete(`/stock/${id}`, { preserveScroll: true });
            }
        },
        consume(id) {
            this.$inertia.post(`/stock/${id}/consume`, {}, { preserveScroll: true });
        },
    },
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Stock congélateur</h1>
                <p class="mt-1 text-sm text-slate-500">Toujours avoir un plat prêt sous la main.</p>
            </div>
            <div class="flex gap-3">
                <div class="rounded-xl bg-sky-50 px-4 py-2 text-center">
                    <div class="text-lg font-bold text-sky-700">{{ totalPortions }}</div>
                    <div class="text-xs text-sky-600">portions</div>
                </div>
                <div class="rounded-xl bg-slate-100 px-4 py-2 text-center">
                    <div class="text-lg font-bold text-slate-700">{{ totalCalories.toLocaleString('fr-CH') }}</div>
                    <div class="text-xs text-slate-500">kcal au total</div>
                </div>
            </div>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Ajouter un plat</h2>
            <form @submit.prevent="submit" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <input v-model="form.name" type="text" placeholder="Nom (ex: Bolognaise)" required
                    class="rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500 sm:col-span-2" />
                <input v-model="form.calories_per_portion" type="number" placeholder="Kcal / portion" required
                    class="rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" />
                <input v-model="form.portions" type="number" placeholder="Nb portions" required
                    class="rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" />
                <button type="submit" :disabled="form.processing"
                    class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-medium text-white hover:bg-sky-600 sm:col-span-4">
                    Ajouter au congélateur
                </button>
            </form>
        </section>

        <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="meal in meals" :key="meal.id"
                class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div v-if="editingId === meal.id" class="space-y-2">
                    <input v-model="editForm.name" class="w-full rounded-lg border-slate-300 text-sm" />
                    <div class="flex gap-2">
                        <input v-model="editForm.calories_per_portion" type="number" class="w-1/2 rounded-lg border-slate-300 text-sm" />
                        <input v-model="editForm.portions" type="number" class="w-1/2 rounded-lg border-slate-300 text-sm" />
                    </div>
                    <div class="flex gap-2">
                        <button @click="saveEdit(meal.id)" class="rounded-full bg-sky-500 px-3 py-1 text-xs font-medium text-white">Sauver</button>
                        <button @click="editingId = null" class="rounded-full border border-slate-200 px-3 py-1 text-xs">Annuler</button>
                    </div>
                </div>
                <div v-else>
                    <div class="flex items-start justify-between">
                        <h3 class="font-semibold text-slate-800">{{ meal.name }}</h3>
                        <span class="rounded-full bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-700">
                            {{ meal.portions }} portion{{ meal.portions > 1 ? 's' : '' }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-slate-500">{{ meal.calories_per_portion }} kcal / portion</p>
                    <p class="text-xs text-slate-400">{{ meal.total_calories.toLocaleString('fr-CH') }} kcal au total</p>
                    <div class="mt-3 flex gap-2">
                        <button @click="consume(meal.id)" :disabled="meal.portions < 1"
                            class="rounded-full bg-sky-500 px-3 py-1 text-xs font-medium text-white hover:bg-sky-600 disabled:opacity-30">
                            Manger 1 portion
                        </button>
                        <button @click="startEdit(meal)" class="rounded-full border border-slate-200 px-3 py-1 text-xs text-slate-600 hover:bg-slate-50">
                            Modifier
                        </button>
                        <button @click="remove(meal.id)" class="rounded-full border border-red-200 px-3 py-1 text-xs text-red-500 hover:bg-red-50">
                            Suppr.
                        </button>
                    </div>
                </div>
            </div>

            <p v-if="!meals.length" class="col-span-full py-10 text-center text-sm text-slate-400">
                Aucun plat en stock. Ajoute ton premier plat préparé ci-dessus !
            </p>
        </section>
    </div>
</template>
