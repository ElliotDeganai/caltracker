<script>
import { Link, Head, usePage } from '@inertiajs/vue3';

const MESSAGES = {
    403: {
        title: 'Accès refusé',
        text: "Tu n'as pas la permission d'accéder à cette page.",
    },
    404: {
        title: 'Page introuvable',
        text: "Cette page n'existe pas ou a été déplacée.",
    },
    419: {
        title: 'Session expirée',
        text: 'Ta session a expiré. Merci de réessayer.',
    },
    429: {
        title: 'Trop de requêtes',
        text: 'Tu as effectué trop de requêtes. Réessaie dans un instant.',
    },
    500: {
        title: 'Erreur serveur',
        text: "Une erreur inattendue s'est produite. Réessaie plus tard.",
    },
    503: {
        title: 'Maintenance en cours',
        text: "L'application est en maintenance. Reviens bientôt.",
    },
};

export default {
    components: { Link, Head },
    props: {
        status: Number,
    },
    computed: {
        settings() {
            return usePage().props.appSettings || { app_name: 'CalTracker', logo_url: null };
        },
        info() {
            return MESSAGES[this.status] || { title: 'Erreur', text: "Quelque chose s'est mal passé." };
        },
    },
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4">
        <Head :title="`${status} — ${info.title}`" />

        <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <img v-if="settings.logo_url" :src="settings.logo_url" alt="Logo" class="mx-auto mb-4 h-10 w-10 rounded-lg object-cover" />
            <div v-else class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-sky-500 font-bold text-white">
                {{ settings.app_name?.charAt(0) || 'C' }}
            </div>

            <p class="text-sm font-medium text-sky-600">Erreur {{ status }}</p>
            <h1 class="mt-1 text-xl font-bold text-slate-800">{{ info.title }}</h1>
            <p class="mt-2 text-sm text-slate-500">{{ info.text }}</p>

            <Link href="/" class="mt-6 inline-block rounded-full bg-sky-500 px-5 py-2 text-sm font-medium text-white hover:bg-sky-600">
                Retour à l'accueil
            </Link>
        </div>
    </div>
</template>
