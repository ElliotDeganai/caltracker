<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

export default {
    components: { AppLayout },
    layout: AppLayout,
    props: {
        last7: Array,
        rollingAvg: Number,
        rollingCount: Number,
        weeklyTarget: Number,
        suggestion: Number,
        todayCalories: Number,
        last7Weights: { type: Array, default: () => [] },
        smoothedToday: Number,
        weightDelta: Number,
        todayWeight: Number,
        stockTotal: Number,
        stockPortions: Number,
        last4Weeks: { type: Array, default: () => [] },
    },
    data() {
        return {
            hoveredWeightIndex: null,
        };
    },
    setup(props) {
        const calorieForm = useForm({
            date: new Date().toISOString().slice(0, 10),
            calories: props.todayCalories ?? '',
            note: '',
        });
        const weightForm = useForm({
            date: new Date().toISOString().slice(0, 10),
            weight_kg: props.todayWeight ?? '',
        });

        return { calorieForm, weightForm };
    },
    computed: {
        // --- Graphique calories ---
        chartWidth() { return 700; },
        chartHeight() { return 180; },
        chartPadding() { return 24; },
        leftMargin() { return 70; },
        maxValue() {
            return Math.max(this.weeklyTarget * 1.3, ...this.last7.map(d => d.calories || 0), 1);
        },
        plotHeight() {
            return this.chartHeight - this.chartPadding - 16;
        },
        targetY() {
            return this.chartHeight - this.chartPadding - (this.weeklyTarget / this.maxValue) * this.plotHeight;
        },
        barWidth() {
            return ((this.chartWidth - this.leftMargin) / this.last7.length) * 0.5;
        },

        // --- Graphique poids ---
        weightChartWidth() { return 600; },
        weightChartHeight() { return 130; },
        weightScale() {
            const values = this.last7Weights.map(d => d.smoothed).filter(v => v !== null);
            const rawValues = this.last7Weights.map(d => d.weight).filter(v => v !== null);
            const all = [...values, ...rawValues];
            if (!all.length) return { min: 0, max: 1 };
            const min = Math.min(...all);
            const max = Math.max(...all);
            const pad = (max - min) * 0.15 || 1;
            return { min: min - pad, max: max + pad };
        },
        weightRawPoints() {
            const { min, max } = this.weightScale;
            const range = max - min || 1;
            return this.last7Weights
                .map((d, i) => d.weight === null ? null : {
                    x: (i / (this.last7Weights.length - 1)) * this.weightChartWidth,
                    y: this.weightChartHeight - ((d.weight - min) / range) * this.weightChartHeight,
                    value: d.weight,
                    label: d.label,
                })
                .filter(Boolean);
        },
        weightSmoothedPoints() {
            const { min, max } = this.weightScale;
            const range = max - min || 1;
            return this.last7Weights
                .map((d, i) => d.smoothed === null ? null : {
                    x: (i / (this.last7Weights.length - 1)) * this.weightChartWidth,
                    y: this.weightChartHeight - ((d.smoothed - min) / range) * this.weightChartHeight,
                })
                .filter(Boolean);
        },
        weightSmoothedLine() {
            return this.weightSmoothedPoints.map(p => `${p.x},${p.y}`).join(' ');
        },
        weightTrendColor() {
            if (this.weightDelta === null) return '#94a3b8';
            return this.weightDelta > 0 ? '#dc2626' : '#059669';
        },
        tooltipPosition() {
            if (this.hoveredWeightIndex === null) return { left: '0%', top: '0%' };
            const p = this.weightRawPoints[this.hoveredWeightIndex];
            return {
                left: (p.x / this.weightChartWidth * 100) + '%',
                top: (p.y / this.weightChartHeight * 100) + '%',
            };
        },
    },
    methods: {
        submitCalories() {
            this.calorieForm.post('/calories', { preserveScroll: true });
        },
        submitWeight() {
            this.weightForm.post('/poids', { preserveScroll: true });
        },
        barX(i) {
            const slot = (this.chartWidth - this.leftMargin) / this.last7.length;
            return this.leftMargin + slot * i + (slot - this.barWidth) / 2;
        },
        barHeight(calories) {
            if (!calories) return 0;
            return (calories / this.maxValue) * this.plotHeight;
        },
        barY(calories) {
            return this.chartHeight - this.chartPadding - this.barHeight(calories);
        },
        barColor(calories) {
            if (!calories) return '#cbd5e1';
            return calories > this.weeklyTarget ? '#dc2626' : '#059669';
        },
        weekDelta(i) {
            if (i === 0) return null;
            const cur = this.last4Weeks[i]?.average;
            const prev = this.last4Weeks[i - 1]?.average;
            if (cur === null || prev === null || cur === undefined || prev === undefined) return null;
            return +(cur - prev).toFixed(2);
        },
    },
};
</script>

<template>
    <div class="space-y-8">

        <section class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="text-xs text-slate-400">Moyenne 7j</div>
                <div class="text-xl font-bold sm:text-2xl"
                    :class="rollingAvg === null ? 'text-slate-800' : (rollingAvg > weeklyTarget ? 'text-red-600' : 'text-emerald-600')">
                    {{ rollingAvg ?? '-' }}
                </div>
                <div class="text-xs text-slate-400">kcal/j · sur {{ rollingCount }}/7 jours</div>
            </div>
            <div class="rounded-2xl bg-sky-50 p-4 shadow-sm">
                <div class="text-xs text-sky-600">Objectif semaine</div>
                <div class="text-xl font-bold text-sky-700 sm:text-2xl">{{ weeklyTarget }}</div>
                <div class="text-xs text-sky-500">kcal / jour</div>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="text-xs text-slate-400">Suggestion aujourd'hui</div>
                <div class="text-xl font-bold text-slate-800 sm:text-2xl">{{ suggestion }}</div>
                <div class="text-xs text-slate-400">kcal pour équilibrer</div>
            </div>
        </section>

        <!-- Graphique calories -->
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-1 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-sm font-semibold text-slate-700">7 derniers jours</h2>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">
                    — Objectif : {{ weeklyTarget }} kcal
                </span>
            </div>
            <div class="mb-3 flex items-center gap-3 text-xs text-slate-400">
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Sous l'objectif</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-red-500"></span>Dépassé</span>
            </div>

            <svg :viewBox="`0 0 ${chartWidth} ${chartHeight}`" class="h-[140px] w-full sm:h-[180px]">
                <line :x1="0" :y1="targetY" :x2="chartWidth" :y2="targetY"
                    stroke="#94a3b8" stroke-width="1.5" stroke-dasharray="6 4" />

                <g v-for="(day, i) in last7" :key="day.date">
                    <rect :x="barX(i)" :y="barY(day.calories)" :width="barWidth" :height="barHeight(day.calories)"
                        :fill="barColor(day.calories)" rx="4" />
                    <text :x="barX(i) + barWidth / 2" :y="chartHeight - 4" text-anchor="middle" font-size="11" fill="#94a3b8">
                        {{ day.label }}
                    </text>
                    <text :x="barX(i) + barWidth / 2" :y="barY(day.calories) - 6" text-anchor="middle" font-size="11"
                        :fill="barColor(day.calories)" font-weight="600">
                        {{ day.calories ?? '-' }}
                    </text>
                </g>
            </svg>
        </section>

        <!-- Tableau récapitulatif poids par semaine -->
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-sm font-semibold text-slate-700">Poids moyen — 4 dernières semaines</h2>

            <table class="hidden w-full text-sm sm:table">
                <thead>
                    <tr class="border-b border-slate-100 text-left text-xs text-slate-400">
                        <th class="pb-2 font-medium">Semaine</th>
                        <th class="pb-2 font-medium">Moyenne</th>
                        <th class="pb-2 font-medium">Évolution</th>
                        <th class="pb-2 font-medium">Jours renseignés</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(week, i) in last4Weeks" :key="i" class="border-b border-slate-50 last:border-0">
                        <td class="py-2.5 text-slate-600">{{ week.week_start }} – {{ week.week_end }}</td>
                        <td class="py-2.5 font-semibold text-slate-800">{{ week.average ?? '-' }} kg</td>
                        <td class="py-2.5">
                            <span v-if="weekDelta(i) === null" class="text-slate-300">—</span>
                            <span v-else class="font-medium" :class="weekDelta(i) > 0 ? 'text-red-600' : 'text-emerald-600'">
                                {{ weekDelta(i) > 0 ? '+' : '' }}{{ weekDelta(i) }} kg
                            </span>
                        </td>
                        <td class="py-2.5 text-slate-400">{{ week.days_logged }}/7</td>
                    </tr>
                </tbody>
            </table>

            <div class="space-y-2 sm:hidden">
                <div v-for="(week, i) in last4Weeks" :key="i" class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5">
                    <div>
                        <div class="text-xs text-slate-400">{{ week.week_start }} – {{ week.week_end }}</div>
                        <div class="font-semibold text-slate-800">{{ week.average ?? '-' }} kg</div>
                    </div>
                    <div class="text-right">
                        <span v-if="weekDelta(i) === null" class="text-xs text-slate-300">—</span>
                        <span v-else class="text-sm font-medium" :class="weekDelta(i) > 0 ? 'text-red-600' : 'text-emerald-600'">
                            {{ weekDelta(i) > 0 ? '+' : '' }}{{ weekDelta(i) }} kg
                        </span>
                        <div class="text-xs text-slate-400">{{ week.days_logged }}/7 jours</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Saisie calories -->
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-3 text-sm font-semibold text-slate-700">Enregistrer mes calories</h2>
                <form @submit.prevent="submitCalories" class="space-y-3">
                    <input v-model="calorieForm.date" type="date" class="w-full rounded-lg border-slate-300 text-sm" />
                    <input v-model="calorieForm.calories" type="number" placeholder="Calories" required
                        class="w-full rounded-lg border-slate-300 text-sm" />
                    <input v-model="calorieForm.note" type="text" placeholder="Note (optionnel)"
                        class="w-full rounded-lg border-slate-300 text-sm" />
                    <button type="submit" :disabled="calorieForm.processing"
                        class="w-full rounded-lg bg-sky-500 px-4 py-2 text-sm font-medium text-white hover:bg-sky-600">
                        Enregistrer
                    </button>
                </form>
            </section>

            <!-- Poids -->
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-1 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-sm font-semibold text-slate-700">Poids — 7 derniers jours</h2>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">
                        — Moyenne 7j : {{ smoothedToday ?? '-' }} kg
                    </span>
                </div>
                <div class="mb-3">
                    <span class="rounded-full px-2.5 py-1 text-xs font-medium"
                        :class="weightDelta === null ? 'bg-slate-100 text-slate-500' : (weightDelta > 0 ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600')">
                        {{ weightDelta !== null ? (weightDelta > 0 ? '+' : '') + weightDelta + ' kg vs sem. dernière' : 'Pas assez de données' }}
                    </span>
                </div>

                <div class="relative">
                    <svg :viewBox="`0 0 ${weightChartWidth} ${weightChartHeight}`" class="h-[120px] w-full sm:h-[140px]">
                        <polyline :points="weightSmoothedLine" fill="none" :stroke="weightTrendColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round" />

                        <g v-for="(d, i) in weightRawPoints" :key="'raw-'+i">
                            <circle :cx="d.x" :cy="d.y" r="3" fill="#cbd5e1" />
                            <circle :cx="d.x" :cy="d.y" r="12" fill="transparent"
                                @mouseenter="hoveredWeightIndex = i" @mouseleave="hoveredWeightIndex = null"
                                class="cursor-pointer" />
                            <circle v-if="hoveredWeightIndex === i" :cx="d.x" :cy="d.y" r="5" :fill="weightTrendColor" />
                        </g>
                    </svg>

                    <div v-if="hoveredWeightIndex !== null"
                        class="pointer-events-none absolute -translate-x-1/2 -translate-y-full rounded-lg bg-slate-800 px-2.5 py-1.5 text-xs text-white shadow-lg"
                        :style="{ left: tooltipPosition.left, top: tooltipPosition.top }">
                        <div class="font-semibold">{{ weightRawPoints[hoveredWeightIndex].value }} kg</div>
                        <div class="text-slate-300">{{ weightRawPoints[hoveredWeightIndex].label }}</div>
                    </div>
                </div>

                <div class="mt-2 flex justify-between px-1 text-xs text-slate-400">
                    <span v-for="day in last7Weights" :key="day.date">{{ day.label }}</span>
                </div>

                <form @submit.prevent="submitWeight" class="mt-3 flex flex-wrap gap-2">
                    <input v-model="weightForm.date" type="date" class="rounded-lg border-slate-300 text-sm" />
                    <input v-model="weightForm.weight_kg" type="number" step="0.1" placeholder="Poids (kg)" required
                        class="min-w-0 flex-1 rounded-lg border-slate-300 text-sm" />
                    <button type="submit" :disabled="weightForm.processing"
                        class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-medium text-white hover:bg-sky-600">
                        OK
                    </button>
                </form>
            </section>
        </div>
    </div>
</template>
