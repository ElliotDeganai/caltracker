<script>
import { Link, useForm } from '@inertiajs/vue3';
import AuthSplitLayout from '@/Layouts/AuthSplitLayout.vue';

export default {
    components: { Link, AuthSplitLayout },
    props: {
        canResetPassword: Boolean,
        status: String,
    },
    setup() {
        const form = useForm({ email: '', password: '', remember: false });
        return { form };
    },
    methods: {
        submit() {
            this.form.post('/login', { onFinish: () => this.form.reset('password') });
        },
    },
};
</script>

<template>
    <AuthSplitLayout title="Connexion">
        <h1 class="text-2xl font-bold text-slate-900">Bon retour</h1>
        <p class="mt-1 text-sm text-slate-500">Connecte-toi pour continuer ton suivi.</p>

        <div v-if="status" class="mt-5 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-700">{{ status }}</div>

        <form @submit.prevent="submit" class="mt-8 space-y-5">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">E-mail</label>
                <input v-model="form.email" type="email" required autofocus autocomplete="username" placeholder="toi@exemple.com"
                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20" />
                <div v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">{{ form.errors.email }}</div>
            </div>

            <div>
                <div class="mb-1.5 flex items-center justify-between">
                    <label class="text-sm font-medium text-slate-700">Mot de passe</label>
                    <Link v-if="canResetPassword" href="/forgot-password" class="text-xs font-medium text-sky-600 hover:text-sky-700">Oublié ?</Link>
                </div>
                <input v-model="form.password" type="password" required autocomplete="current-password" placeholder="••••••••"
                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20" />
                <div v-if="form.errors.password" class="mt-1.5 text-xs text-red-500">{{ form.errors.password }}</div>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input v-model="form.remember" type="checkbox" class="rounded border-slate-300 text-sky-500 focus:ring-sky-500" />
                Se souvenir de moi
            </label>

            <button type="submit" :disabled="form.processing"
                class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:opacity-50">
                Se connecter
            </button>

            <p class="text-center text-sm text-slate-500">
                Pas encore de compte ?
                <Link href="/register" class="font-medium text-sky-600 hover:text-sky-700">Créer un compte</Link>
            </p>
        </form>
    </AuthSplitLayout>
</template>
