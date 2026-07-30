<script>
import { Link, useForm } from '@inertiajs/vue3';
import AuthSplitLayout from '@/Layouts/AuthSplitLayout.vue';

export default {
    components: { Link, AuthSplitLayout },
    props: { status: String },
    setup() {
        const form = useForm({ email: '' });
        return { form };
    },
    methods: {
        submit() {
            this.form.post('/forgot-password');
        },
    },
};
</script>

<template>
    <AuthSplitLayout title="Mot de passe oublié" heading="Ça arrive à tout\nle monde." tagline="On t'envoie un lien pour choisir un nouveau mot de passe.">
        <h1 class="text-2xl font-bold text-slate-900">Mot de passe oublié</h1>
        <p class="mt-1 text-sm text-slate-500">Entre ton e-mail, on t'envoie un lien de réinitialisation.</p>

        <div v-if="status" class="mt-5 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-700">{{ status }}</div>

        <form @submit.prevent="submit" class="mt-8 space-y-5">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">E-mail</label>
                <input v-model="form.email" type="email" required autofocus placeholder="toi@exemple.com"
                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20" />
                <div v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">{{ form.errors.email }}</div>
            </div>

            <button type="submit" :disabled="form.processing"
                class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:opacity-50">
                Envoyer le lien
            </button>

            <p class="text-center text-sm text-slate-500">
                <Link href="/login" class="font-medium text-sky-600 hover:text-sky-700">Retour à la connexion</Link>
            </p>
        </form>
    </AuthSplitLayout>
</template>
