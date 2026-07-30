<script>
import { useForm } from '@inertiajs/vue3';
import AuthSplitLayout from '@/Layouts/AuthSplitLayout.vue';

export default {
    components: { AuthSplitLayout },
    props: { email: String, token: String },
    setup(props) {
        const form = useForm({
            token: props.token,
            email: props.email,
            password: '',
            password_confirmation: '',
        });
        return { form };
    },
    methods: {
        submit() {
            this.form.post('/reset-password', { onFinish: () => this.form.reset('password', 'password_confirmation') });
        },
    },
};
</script>

<template>
    <AuthSplitLayout title="Réinitialiser le mot de passe" heading="Presque\nterminé." tagline="Choisis un nouveau mot de passe pour ton compte.">
        <h1 class="text-2xl font-bold text-slate-900">Nouveau mot de passe</h1>
        <p class="mt-1 text-sm text-slate-500">Choisis un mot de passe pour ton compte.</p>

        <form @submit.prevent="submit" class="mt-8 space-y-5">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">E-mail</label>
                <input v-model="form.email" type="email" required autocomplete="username"
                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 transition focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20" />
                <div v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">{{ form.errors.email }}</div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Nouveau mot de passe</label>
                <input v-model="form.password" type="password" required autofocus autocomplete="new-password" placeholder="••••••••"
                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20" />
                <div v-if="form.errors.password" class="mt-1.5 text-xs text-red-500">{{ form.errors.password }}</div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Confirmer le mot de passe</label>
                <input v-model="form.password_confirmation" type="password" required autocomplete="new-password" placeholder="••••••••"
                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20" />
                <div v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-red-500">{{ form.errors.password_confirmation }}</div>
            </div>

            <button type="submit" :disabled="form.processing"
                class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:opacity-50">
                Réinitialiser le mot de passe
            </button>
        </form>
    </AuthSplitLayout>
</template>
