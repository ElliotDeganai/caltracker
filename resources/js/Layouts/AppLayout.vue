<script>
import { Link, Head, usePage } from '@inertiajs/vue3';
import UserSwitcher from '@/Components/UserSwitcher.vue';

export default {
    components: { Link, Head, UserSwitcher },
    props: {
        title: { type: String, default: '' },
    },
    data() {
        return {
            mobileMenuOpen: false,
        };
    },
    computed: {
        settings() {
            return usePage().props.appSettings || {
                app_name: 'CalTracker',
                browser_title: 'CalTracker',
                logo_url: null,
                page_slugs: { dashboard: 'dashboard', stock: 'stock', weight: 'poids', settings: 'parametres' },
            };
        },
        permissions() {
            return usePage().props.auth?.permissions || [];
        },
        navItems() {
            const slugs = this.settings.page_slugs;
            const items = [
                { name: 'Dashboard', route: '/' + slugs.dashboard, match: slugs.dashboard, perm: 'view-dashboard' },
                { name: 'Objectifs', route: '/objectifs', match: 'objectifs', perm: 'view-targets' },
                { name: 'Réglages', route: '/' + slugs.settings, match: slugs.settings, perm: 'view-settings' },
            ];
            return items.filter(item => this.permissions.includes(item.perm));
        },
        currentPath() {
            return window.location.pathname.replace(/^\//, '');
        },
        viewingUser() {
            return usePage().props.admin?.viewingUser || null;
        },
    },
};
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <Head :title="title || settings.browser_title">
            <link v-if="settings.logo_url" rel="icon" :href="settings.logo_url" />
        </Head>

        <!-- Navbar desktop / tablette : sticky en haut -->
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
                <Link :href="'/' + settings.page_slugs.dashboard" class="flex items-center gap-2">
                    <img v-if="settings.logo_url" :src="settings.logo_url" alt="Logo" class="h-9 w-9 rounded-lg object-cover" />
                    <div v-else class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-500 text-white font-bold">
                        {{ settings.app_name?.charAt(0) || 'C' }}
                    </div>
                    <span class="hidden text-lg font-semibold text-slate-800 sm:inline">{{ settings.app_name }}</span>
                </Link>

                <!-- Nav desktop -->
                <nav class="hidden items-center gap-1 md:flex">
                    <Link
                        v-for="item in navItems"
                        :key="item.route"
                        :href="item.route"
                        class="rounded-full px-4 py-2 text-sm font-medium transition"
                        :class="currentPath === item.match
                            ? 'bg-sky-100 text-sky-700'
                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                    >
                        {{ item.name }}
                    </Link>
                </nav>

                <div class="flex items-center gap-3">
                    <UserSwitcher v-if="$page.props.admin?.users" />

                    <div class="hidden items-center gap-2 rounded-full bg-slate-100 py-1 pl-1 pr-3 sm:flex">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-sky-500 text-xs font-semibold text-white">
                            {{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <span class="text-sm font-medium text-slate-700">{{ $page.props.auth?.user?.name }}</span>
                    </div>

                    <Link href="/logout" method="post" as="button"
                        class="hidden rounded-full px-4 py-2 text-sm font-medium text-slate-500 hover:bg-slate-100 sm:inline-block">
                        Déconnexion
                    </Link>

                    <!-- Bouton profil mobile uniquement -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-500 text-sm font-semibold text-white sm:hidden">
                        {{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() }}
                    </button>
                </div>
            </div>

            <!-- Panneau profil mobile -->
            <div v-if="mobileMenuOpen" class="border-t border-slate-100 bg-white px-4 py-3 sm:hidden">
                <div class="mb-3 flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-500 text-xs font-semibold text-white">
                        {{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <span class="text-sm font-medium text-slate-700">{{ $page.props.auth?.user?.name }}</span>
                </div>

                <div v-if="$page.props.admin?.users" class="mb-3">
                    <UserSwitcher mobile />
                </div>

                <Link href="/logout" method="post" as="button"
                    class="block w-full rounded-lg bg-slate-100 px-4 py-2 text-center text-sm font-medium text-slate-600">
                    Déconnexion
                </Link>
            </div>

            <!-- Bandeau : visualisation d'un autre compte (admin) -->
            <div v-if="viewingUser" class="bg-amber-50 px-4 py-2 text-center text-sm text-amber-700">
                Tu visualises et modifies les données de <strong>{{ viewingUser.name }}</strong> —
                <button @click="$inertia.post('/admin/clear-user', {}, { preserveScroll: true })" class="underline">
                    revenir à mon compte
                </button>
            </div>
        </header>

        <!-- Contenu -->
        <main class="mx-auto max-w-6xl px-4 pb-24 pt-6 sm:px-6 md:pb-10">
            <slot />

            <footer class="mt-12 border-t border-slate-200 pt-6 text-center text-xs text-slate-400">
                <p>{{ settings.app_name }} — © {{ new Date().getFullYear() }}</p>
            </footer>
        </main>

        <!-- Bottom nav mobile : fixe en bas -->
        <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 backdrop-blur md:hidden">
            <div class="flex items-center justify-around py-2">
                <Link
                    v-for="item in navItems"
                    :key="item.route"
                    :href="item.route"
                    class="flex flex-col items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium transition"
                    :class="currentPath === item.match ? 'text-sky-600' : 'text-slate-500'"
                >
                    <span class="h-5 w-5 rounded-full"
                        :class="currentPath === item.match ? 'bg-sky-500' : 'bg-slate-300'"></span>
                    {{ item.name }}
                </Link>
            </div>
        </nav>
    </div>
</template>
