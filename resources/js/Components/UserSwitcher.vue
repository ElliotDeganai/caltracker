<script>
import { router, usePage } from '@inertiajs/vue3';

export default {
    props: {
        mobile: { type: Boolean, default: false },
    },
    data() {
        return {
            open: false,
            searchQuery: '',
        };
    },
    computed: {
        users() {
            return usePage().props.admin?.users || [];
        },
        viewingUser() {
            return usePage().props.admin?.viewingUser || null;
        },
        filteredUsers() {
            const q = this.searchQuery.trim().toLowerCase();
            if (!q) return this.users;
            return this.users.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
        },
    },
    methods: {
        selectUser(userId) {
            this.open = false;
            this.searchQuery = '';
            if (userId) {
                router.post('/admin/switch-user', { user_id: userId }, { preserveScroll: true });
            } else {
                router.post('/admin/clear-user', {}, { preserveScroll: true });
            }
        },
    },
};
</script>

<template>
    <div :class="mobile ? 'relative w-full' : 'relative hidden sm:block'">
        <button @click="open = !open"
            :class="mobile
                ? 'flex w-full items-center justify-between rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600'
                : 'flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-200'">
            {{ viewingUser?.name || 'Mon compte' }}
            <span class="text-slate-400">▾</span>
        </button>

        <div v-if="open" :class="mobile ? 'mt-2 w-full' : 'absolute right-0 top-full z-50 mt-2 w-64'"
            class="rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Rechercher un utilisateur..."
                class="mb-2 w-full rounded-lg border-slate-200 text-sm"
                autofocus
            />
            <div class="max-h-64 overflow-y-auto">
                <button @click="selectUser('')"
                    class="block w-full rounded-lg px-3 py-2 text-left text-sm hover:bg-slate-50"
                    :class="!viewingUser ? 'bg-sky-50 text-sky-700 font-medium' : 'text-slate-600'">
                    — Mon compte —
                </button>
                <button v-for="u in filteredUsers" :key="u.id" @click="selectUser(u.id)"
                    class="block w-full rounded-lg px-3 py-2 text-left text-sm hover:bg-slate-50"
                    :class="viewingUser?.id === u.id ? 'bg-sky-50 text-sky-700 font-medium' : 'text-slate-600'">
                    <div>{{ u.name }}</div>
                    <div class="text-xs text-slate-400">{{ u.email }}</div>
                </button>
                <p v-if="!filteredUsers.length" class="px-3 py-4 text-center text-xs text-slate-400">Aucun résultat</p>
            </div>
        </div>
    </div>
</template>
