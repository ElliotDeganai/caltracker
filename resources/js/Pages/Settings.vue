<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

export default {
    components: { AppLayout },
    layout: AppLayout,
    props: {
        settings: Object,
    },
    data() {
        return {
            logoPreview: this.settings.logo_url,
        };
    },
    setup(props) {
        const generalForm = useForm({
            app_name: props.settings.app_name,
            browser_title: props.settings.browser_title,
        });

        const logoForm = useForm({
            logo: null,
        });

        const slugsForm = useForm({
            slugs: { ...props.settings.page_slugs },
        });

        return { generalForm, logoForm, slugsForm };
    },
    methods: {
        submitGeneral() {
            this.generalForm.post('/settings/general', { preserveScroll: true });
        },
        onLogoChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.logoForm.logo = file;
            this.logoPreview = URL.createObjectURL(file);
        },
        submitLogo() {
            this.logoForm.post('/settings/logo', {
                preserveScroll: true,
                forceFormData: true,
            });
        },
        removeLogo() {
            this.logoForm.delete('/settings/logo', { preserveScroll: true });
            this.logoPreview = null;
        },
        submitSlugs() {
            this.slugsForm.post('/settings/urls', { preserveScroll: true });
        },
    },
};
</script>

<template>
    <div class="mx-auto max-w-2xl space-y-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Réglages de l'application</h1>
            <p class="mt-1 text-sm text-slate-500">Logo, nom, titre et URLs des pages.</p>
        </div>

        <!-- Logo -->
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Logo</h2>
            <div class="flex items-center gap-5">
                <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                    <img v-if="logoPreview" :src="logoPreview" class="h-full w-full object-cover" />
                    <span v-else class="text-xs text-slate-400">Aucun</span>
                </div>
                <div class="flex-1">
                    <input type="file" accept=".png,.jpg,.jpeg,.svg" @change="onLogoChange"
                        class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-sky-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-sky-700 hover:file:bg-sky-100" />
                    <p class="mt-1 text-xs text-slate-400">PNG, JPG ou SVG — 2 Mo max.</p>
                    <div v-if="logoForm.errors.logo" class="mt-1 text-xs text-red-500">{{ logoForm.errors.logo }}</div>
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <button @click="submitLogo" :disabled="!logoForm.logo || logoForm.processing"
                    class="rounded-full bg-sky-500 px-4 py-2 text-sm font-medium text-white hover:bg-sky-600 disabled:opacity-40">
                    Enregistrer le logo
                </button>
                <button v-if="settings.logo_url" @click="removeLogo"
                    class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Supprimer
                </button>
            </div>
        </section>

        <!-- Nom & titre -->
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Nom & titre du navigateur</h2>
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Nom de l'application (affiché dans la navbar)</label>
                    <input v-model="generalForm.app_name" type="text"
                        class="w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500" />
                    <div v-if="generalForm.errors.app_name" class="mt-1 text-xs text-red-500">{{ generalForm.errors.app_name }}</div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Titre de l'onglet navigateur</label>
                    <input v-model="generalForm.browser_title" type="text"
                        class="w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500" />
                    <div v-if="generalForm.errors.browser_title" class="mt-1 text-xs text-red-500">{{ generalForm.errors.browser_title }}</div>
                </div>
            </div>
            <button @click="submitGeneral" :disabled="generalForm.processing"
                class="mt-4 rounded-full bg-sky-500 px-4 py-2 text-sm font-medium text-white hover:bg-sky-600 disabled:opacity-40">
                Enregistrer
            </button>
        </section>

        <!-- URLs des pages -->
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-1 text-lg font-semibold text-slate-800">URLs des pages</h2>
            <p class="mb-4 text-sm text-slate-500">Personnalise le slug de chaque page. Les anciennes URLs redirigeront automatiquement en 301.</p>
            <div class="space-y-4">
                <div v-for="(value, page) in slugsForm.slugs" :key="page">
                    <label class="mb-1 block text-sm font-medium capitalize text-slate-600">{{ page }}</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-400">/</span>
                        <input v-model="slugsForm.slugs[page]" type="text"
                            class="w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500" />
                    </div>
                    <div v-if="slugsForm.errors[`slugs.${page}`]" class="mt-1 text-xs text-red-500">
                        {{ slugsForm.errors[`slugs.${page}`] }}
                    </div>
                </div>
            </div>
            <div v-if="slugsForm.errors.slugs" class="mt-2 text-xs text-red-500">{{ slugsForm.errors.slugs }}</div>
            <button @click="submitSlugs" :disabled="slugsForm.processing"
                class="mt-4 rounded-full bg-sky-500 px-4 py-2 text-sm font-medium text-white hover:bg-sky-600 disabled:opacity-40">
                Enregistrer les URLs
            </button>
            <p class="mt-2 text-xs text-amber-600">
                Si les routes sont mises en cache en production (<code>route:cache</code>), lance <code>php artisan route:clear</code> après un changement.
            </p>
        </section>
    </div>
</template>
