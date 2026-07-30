<script>
import { useForm } from '@inertiajs/vue3';
import AuthSplitLayout from '@/Layouts/AuthSplitLayout.vue';

export default {
    components: { AuthSplitLayout },
    setup() {
        const form = useForm({ password: '' });
        return { form };
    },
    methods: {
        submit() {
            this.form.post('/confirm-password', { onFinish: () => this.form.reset() });
        },
    },
};
</script>

<template>
    <AuthSplitLayout title="Confirmer le mot de passe" heading="Zone\nsécurisée." tagline="Confirme ton mot de passe avant de continuer.">
        <h1 class="text-2xl font-bold text-slate-900">Confirmer le mot de passe</h1>
        <p class="mt-1 text-sm text-slate-500">C'est une zone sécurisée, confirme ton mot de passe.</p>

        <form @submit.prevent="submit" class="mt-8 space-y-5">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Mot de passe</label>
                <input v-model="form.password" type="password" required autofocus autocomplete="current-password" placeholder="••••••••"
                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20" />
                <div v-if="form.errors.password" class="mt-1.5 text-xs text-red-500">{{ form.errors.password }}</div>
            </div>

            <button type="submit" :disabled="form.processing"
                class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:opacity-50">
                Confirmer
            </button>
        </form>
    </AuthSplitLayout>
</template>
