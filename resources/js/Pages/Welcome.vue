<script>
import { Head, Link, usePage } from '@inertiajs/vue3';

export default {
    components: { Head, Link },
    computed: {
        settings() {
            return usePage().props.appSettings || { app_name: 'CalTracker', logo_url: null };
        },
    },
    data() {
        return {
            features: [
                {
                    icon: 'calories',
                    title: 'Suivi calorique',
                    text: "Un écart un jour se rattrape le lendemain — on regarde la tendance, pas la performance du jour.",
                },
                {
                    icon: 'weight',
                    title: 'Suivi de poids intelligent',
                    text: "Moyenne glissante pour ignorer le bruit des fluctuations quotidiennes et voir la vraie tendance.",
                },
/*                 {
                    icon: 'stock',
                    title: 'Stock de plats préparés',
                    text: "Toujours un plat prêt au congélateur pour éviter de craquer un soir de flemme.",
                }, */
                {
                    icon: 'target',
                    title: 'Objectifs par semaine',
                    text: "Ajuste ta cible calorique semaine après semaine, selon ton évolution réelle.",
                },
            ],
        };
    },
};
</script>

<template>
    <div class="min-h-screen bg-white">
        <Head title="Bienvenue" />

        <!-- Hero -->
        <div class="relative overflow-hidden bg-gradient-to-br from-sky-500 via-sky-600 to-cyan-600">
            <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-white/10"></div>

            <div class="relative z-10 mx-auto grid max-w-5xl grid-cols-1 gap-10 px-6 py-16 sm:py-24 lg:grid-cols-2 lg:items-center">
                <div>
                    <div class="mb-8 flex items-center gap-2">
                        <img v-if="settings.logo_url" :src="settings.logo_url" alt="Logo" class="h-9 w-9 rounded-lg object-cover" />
                        <div v-else class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/20 text-lg font-bold text-white backdrop-blur">
                            {{ settings.app_name?.charAt(0) || 'C' }}
                        </div>
                        <span class="text-lg font-semibold text-white">{{ settings.app_name }}</span>
                    </div>

                    <h1 class="max-w-2xl text-3xl font-bold leading-tight text-white sm:text-5xl">
                        Équilibre tes calories,<br />sans jamais culpabiliser.
                    </h1>
                    <p class="mt-5 max-w-lg text-base text-sky-100 sm:text-lg">
                        Un suivi qui lisse tes calories sur 7 jours, garde un œil sur ton poids et t'aide à atteindre tes objectifs.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link href="/login" class="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-50">
                            Se connecter
                        </Link>
                        <Link href="/register" class="rounded-xl border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            Créer un compte
                        </Link>
                    </div>
                </div>

                <!-- Illustration : aperçu du dashboard flottant -->
                <div class="block">
                    <div class="rotate-2 rounded-2xl bg-white p-5 shadow-2xl">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex gap-1.5">
                                <div class="h-2.5 w-2.5 rounded-full bg-red-300"></div>
                                <div class="h-2.5 w-2.5 rounded-full bg-amber-300"></div>
                                <div class="h-2.5 w-2.5 rounded-full bg-emerald-300"></div>
                            </div>
                            <div class="h-2 w-16 rounded-full bg-slate-100"></div>
                        </div>

                        <div class="mb-4 grid grid-cols-3 gap-2">
                            <div class="rounded-lg bg-slate-50 p-2">
                                <div class="text-[10px] text-slate-400">Moyenne 7j</div>
                                <div class="text-sm font-bold text-emerald-600">1950</div>
                            </div>
                            <div class="rounded-lg bg-sky-50 p-2">
                                <div class="text-[10px] text-sky-500">Objectif</div>
                                <div class="text-sm font-bold text-sky-700">2100</div>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-2">
                                <div class="text-[10px] text-slate-400">Suggestion</div>
                                <div class="text-sm font-bold text-slate-700">2250</div>
                            </div>
                        </div>

                        <svg viewBox="0 0 240 70" class="mb-3 h-16 w-full">
                            <line x1="0" y1="20" x2="240" y2="20" stroke="#94a3b8" stroke-width="1" stroke-dasharray="4 3" />
                            <rect x="10" y="30" width="18" height="30" rx="3" fill="#10b981" />
                            <rect x="40" y="10" width="18" height="50" rx="3" fill="#ef4444" />
                            <rect x="70" y="35" width="18" height="25" rx="3" fill="#10b981" />
                            <rect x="100" y="40" width="18" height="20" rx="3" fill="#10b981" />
                            <rect x="130" y="15" width="18" height="45" rx="3" fill="#ef4444" />
                            <rect x="160" y="32" width="18" height="28" rx="3" fill="#10b981" />
                            <rect x="190" y="25" width="18" height="35" rx="3" fill="#10b981" />
                        </svg>

                        <div class="flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8L13 9l-4 4-6-6" />
                            </svg>
                            <span class="text-xs font-medium text-emerald-700">-0.6 kg cette semaine</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fonctionnalités -->
        <div class="mx-auto max-w-5xl px-6 py-16 sm:py-20">
            <h2 class="text-center text-2xl font-bold text-slate-900">Tout ce qu'il te faut, rien de plus</h2>
            <p class="mx-auto mt-2 max-w-md text-center text-sm text-slate-500">
                Pensé pour tenir dans la durée, pas pour te noyer sous les fonctionnalités.
            </p>

            <div class="mt-12 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div v-for="f in features" :key="f.title" class="rounded-2xl border border-slate-200 p-6 transition hover:border-sky-200 hover:shadow-sm">
                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                        <svg v-if="f.icon === 'calories'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <svg v-else-if="f.icon === 'weight'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a3 3 0 016 0v6M5 10a7 7 0 1114 0v9H5v-9z" />
                        </svg>
<!--                         <svg v-else-if="f.icon === 'stock'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg> -->
                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="12" cy="12" r="5" stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="12" cy="12" r="1" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-800">{{ f.title }}</h3>
                    <p class="mt-1.5 text-sm text-slate-500">{{ f.text }}</p>
                </div>
            </div>
        </div>

        <footer class="border-t border-slate-100 py-8 text-center text-xs text-slate-400">
            {{ settings.app_name }} — © {{ new Date().getFullYear() }}
        </footer>
    </div>
</template>
