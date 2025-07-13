<template>
    <div class="mt-6">
        <!-- Prepínač -->
        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 mb-4">
            <button
                v-for="option in options"
                :key="option"
                @click="mode = optionToMode(option)"
                :class="[
                    'px-4 py-1 rounded-full transition-all font-medium',
                    mode === optionToMode(option)
                        ? 'bg-white text-black shadow'
                        : 'text-gray-500 hover:text-black',
                ]"
            >
                {{ option }}
            </button>
        </div>

        <!-- Graf -->
        <ApexChart
            width="100%"
            type="area"
            :options="chartOptions"
            :series="series"
            height="320"
        />
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import ApexChart from "vue3-apexcharts";

const props = defineProps({
    data: Object,
});

const options = ["Price", "Market cap"];
const mode = ref("price");

// Pomocná funkcia na prevod textu na kľúč dát
const optionToMode = (option) =>
    option.toLowerCase().includes("price") ? "price" : "market_cap";

// Výpočet dátovej série na základe režimu
const series = computed(() => {
    const points =
        mode.value === "price"
            ? props.data.chart_price
            : props.data.chart_market_cap;

    return [
        {
            name: mode.value === "price" ? "Price" : "Market Cap",
            data: points.map(([timestamp, value]) => ({
                x: new Date(timestamp),
                y: parseFloat(value.toFixed(2)),
            })),
        },
    ];
});

// Konfigurácia grafu
const chartOptions = computed(() => ({
    chart: {
        type: "area",
        toolbar: { show: false },
        zoom: { enabled: false },
    },
    colors: ["#10B981"], // zelená
    dataLabels: { enabled: false },
    stroke: {
        curve: "smooth",
        width: 2,
    },
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0,
            stops: [0, 90, 100],
        },
    },
    tooltip: {
        x: {
            format: "dd MMM yyyy HH:mm",
        },
        y: {
            formatter: (val) =>
                mode.value === "price"
                    ? "$" + val.toFixed(2)
                    : "$" + (val / 1e9).toFixed(2) + "B",
        },
    },
    xaxis: {
        type: "datetime",
        labels: {
            datetimeFormatter: {
                day: "dd MMM",
            },
        },
    },
    yaxis: {
        labels: {
            formatter: (val) => {
                if (mode.value === "price") return "$" + val.toFixed(2);
                return "$" + (val / 1e9).toFixed(1) + "B";
            },
        },
    },
    grid: { borderColor: "#e5e7eb" },
}));
</script>
