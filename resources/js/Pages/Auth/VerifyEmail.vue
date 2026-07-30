<script>
import { Link, useForm } from '@inertiajs/vue3';
import AuthSplitLayout from '@/Layouts/AuthSplitLayout.vue';

export default {
    components: { Link, AuthSplitLayout },
    props: { status: String },
    setup() {
        const form = useForm({});
        return { form };
    },
    computed: {
        verificationLinkSent() {
            return this.status === 'verification-link-sent';
        },
    },
    methods: {
        submit() {
            this.form.post('/email/verification-notification');
        },
    },
};
</script>

<template>
    <AuthSplitLayout title="Vérifier l'e-mail" heading="Plus qu'une\nétape." tagline="Confirme ton adresse e-mail pour activer ton compte.">
        <h1 class="text-2xl font-bold text-slate-900">Vérifie ton e-mail</h1>
        <p class="mt-1 text-sm text-slate-500">Merci de confirmer ton adresse via le lien qu'on vient de t'envoyer.</p>

        <div v-if="verificationLinkSent" class="mt-5 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
            Un nouveau lien de vérification a été envoyé à ton adresse e-mail.
        </div>

        <form @submit.prevent="submit" class="mt-8 space-y-4">
            <button type="submit" :disabled="form.processing"
                class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:opacity-50">
                Renvoyer l'e-mail de vérification
            </button>

            <Link href="/logout" method="post" as="button"
                class="w-full text-center text-sm text-slate-500 hover:text-slate-700">
                Se déconnecter
            </Link>
        </form>
    </AuthSplitLayout>
</template>
