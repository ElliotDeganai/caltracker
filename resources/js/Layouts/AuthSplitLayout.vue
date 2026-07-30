<script>
import { Head, usePage, Link} from '@inertiajs/vue3';

export default {
    components: { Head, Link },
    props: {
        title: { type: String, default: '' },
        heading: { type: String, default: 'Équilibre tes calories,\nsans jamais culpabiliser.' },
        tagline: { type: String, default: 'Lissage sur 7 jours, suivi de poids et stock de plats préparés — tout au même endroit.' },
    },
    computed: {
        settings() {
            return usePage().props.appSettings || { app_name: 'CalTracker', logo_url: null };
        },
    },
};
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white lg:flex-row">
        <Head :title="title" />

        <div class="relative overflow-hidden bg-gradient-to-br from-sky-500 via-sky-600 to-cyan-600 px-6 pb-10 pt-8 lg:flex lg:w-1/2 lg:flex-col lg:justify-between lg:p-12">
            <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10 lg:-right-24 lg:-top-24 lg:h-96 lg:w-96"></div>
            <div class="absolute -bottom-20 -left-10 h-48 w-48 rounded-full bg-white/10 lg:-bottom-32 lg:-left-16 lg:h-80 lg:w-80"></div>

            <Link href="/" class="">
                <div class="relative z-10 flex items-center gap-2">
                    <img v-if="settings.logo_url" :src="settings.logo_url" alt="Logo" class="h-9 w-9 rounded-lg object-cover" />
                    <div v-else class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/20 text-lg font-bold text-white backdrop-blur">
                        {{ settings.app_name?.charAt(0) || 'C' }}
                    </div>
                    <span class="text-lg font-semibold text-white">{{ settings.app_name }}</span>
                </div>
            </Link>

            <div class="relative z-10 mt-6 lg:mt-0">
                <h2 class="whitespace-pre-line text-xl font-bold leading-tight text-white lg:text-3xl">{{ heading }}</h2>
                <p class="mt-2 max-w-sm text-xs text-sky-100 lg:mt-4 lg:text-sm">{{ tagline }}</p>
            </div>

            <p class="relative z-10 hidden text-xs text-sky-100/70 lg:block">© {{ new Date().getFullYear() }} {{ settings.app_name }}</p>
        </div>

        <div class="flex flex-1 items-center justify-center px-6 py-10 lg:w-1/2 lg:py-12">
            <div class="w-full max-w-sm">
                <slot />
            </div>
        </div>
    </div>
</template>
