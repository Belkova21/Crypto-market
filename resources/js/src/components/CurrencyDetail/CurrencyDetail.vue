<template>
    <div
        class="relative bg-white p-6 rounded-2xl max-w-xl mx-auto"
        style="padding: 10px; margin-left: 10px"
    >
        <button
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-14xl font-bold leading-none"
            @click="closeDetail"
        >
            &times;
        </button>

        <!-- Title -->
        <div class="flex items-center space-x-5 mb-6">
            <img
                v-if="data.image"
                :src="data.image"
                alt="Logo"
                class="w-10 h-10 rounded-full"
            />
            <h2 class="text-3xl font-bold text-gray-900">
                {{ data.name }}
                <span class="text-base font-normal text-gray-500">
                    ({{ data.symbol?.toUpperCase() }})
                </span>
            </h2>
        </div>
        <ApexChartCoin v-if="data.charts" :data="data.charts" />

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 gap-4">
            <StatItem
                label="Market Cap"
                :value="formatCurrency(data.fvd)"
                :change="data.market_cap_change_24h"
            />
            <StatItem
                label="Price (Current)"
                :value="formatCurrency(data.current_price, '€')"
            />
            <StatItem
                label="Volume (24h)"
                :value="formatCurrency(data.volume)"
            />
            <StatItem
                label="Price Change (7d)"
                :value="formatPercent(data.price_change_percentage_7d)"
            />
            <StatItem
                label="Total Supply"
                :value="formatSupply(data.total_supply, data.symbol)"
                class="col-span-2"
            />
        </div>
    </div>
</template>

<script setup>
import StatItem from "./StatItem.vue";
import ApexChartCoin from "./ApexChartCoin.vue";

const props = defineProps({
    data: Object,
});

const emit = defineEmits(["close"]);

function closeDetail() {
    emit("close");
}

function formatCurrency(val, symbol = "€") {
    if (!val) return "-";
    const num = parseFloat(val);
    return (
        symbol +
        (num >= 1e9
            ? (num / 1e9).toFixed(2) + "B"
            : num.toLocaleString(undefined, { maximumFractionDigits: 2 }))
    );
}

function formatPercent(val) {
    if (!val) return "-";
    const num = parseFloat(val);
    const sign = num >= 0 ? "+" : "";
    return sign + num.toFixed(2) + "%";
}

function formatSupply(val, symbol = "") {
    if (!val) return "-";
    const supply = parseFloat(val).toLocaleString(undefined, {
        maximumFractionDigits: 2,
    });
    return supply + " " + symbol.toUpperCase();
}
</script>
