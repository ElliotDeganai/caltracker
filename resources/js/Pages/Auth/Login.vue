<script>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

export default {
    components: { Head, Link },
    props: {
        canResetPassword: Boolean,
        status: String,
    },
    computed: {
        settings() {
            return usePage().props.appSettings || { app_name: 'CalTracker', logo_url: null };
        },
    },
    setup() {
        const form = useForm({
            email: '',
            password: '',
            remember: false,
        });

        return { form };
    },
    methods: {
        submit() {
            this.form.post('/login', {
                onFinish: () => this.form.reset('password'),
            });
        },
    },
};
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white lg:flex-row">
        <Head title="Connexion" />

        <!-- Panneau de marque : bandeau incurvé sur mobile, panneau plein écran sur desktop -->
        <div class="relative overflow-hidden bg-gradient-to-br from-sky-500 via-sky-600 to-cyan-600 px-6 pb-10 pt-8 lg:flex lg:w-1/2 lg:flex-col lg:justify-between lg:rounded-none lg:p-12"
             >
            <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10 lg:-right-24 lg:-top-24 lg:h-96 lg:w-96"></div>
            <div class="absolute -bottom-20 -left-10 h-48 w-48 rounded-full bg-white/10 lg:-bottom-32 lg:-left-16 lg:h-80 lg:w-80"></div>

            <div class="relative z-10 flex items-center gap-2">
                <img v-if="settings.logo_url" :src="settings.logo_url" alt="Logo" class="h-9 w-9 rounded-lg object-cover" />
                <div v-else class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/20 text-lg font-bold text-white backdrop-blur">
                    {{ settings.app_name?.charAt(0) || 'C' }}
                </div>
                <span class="text-lg font-semibold text-white">{{ settings.app_name }}</span>
            </div>

            <div class="relative z-10 mt-6 lg:mt-0">
                <h2 class="text-xl font-bold leading-tight text-white lg:text-3xl">
                    Équilibre tes calories,<br />sans jamais culpabiliser.
                </h2>
                <p class="mt-2 max-w-sm text-xs text-sky-100 lg:mt-4 lg:text-sm">
                    Lissage sur 7 jours, suivi de poids et stock de plats préparés — tout au même endroit.
                </p>
            </div>

            <p class="relative z-10 hidden text-xs text-sky-100/70 lg:block">© {{ new Date().getFullYear() }} {{ settings.app_name }}</p>
        </div>

        <!-- Formulaire -->
        <div class="flex flex-1 items-center justify-center px-6 py-10 lg:w-1/2 lg:py-12">
            <div class="w-full max-w-sm">
                <h1 class="text-2xl font-bold text-slate-900">Bon retour</h1>
                <p class="mt-1 text-sm text-slate-500">Connecte-toi pour continuer ton suivi.</p>

                <div v-if="status" class="mt-5 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">E-mail</label>
                        <input v-model="form.email" type="email" required autofocus autocomplete="username"
                            placeholder="toi@exemple.com"
                            class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20" />
                        <div v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label class="text-sm font-medium text-slate-700">Mot de passe</label>
                            <Link v-if="canResetPassword" href="/forgot-password" class="text-xs font-medium text-sky-600 hover:text-sky-700">
                                Oublié ?
                            </Link>
                        </div>
                        <input v-model="form.password" type="password" required autocomplete="current-password"
                            placeholder="••••••••"
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
                </form>
            </div>
        </div>
    </div>
</template>
