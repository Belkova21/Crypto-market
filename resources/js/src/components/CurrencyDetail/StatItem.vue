<template>
    <div
        class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 text-center"
    >
        <p class="text-gray-500 text-xs mb-1">{{ label }}</p>
        <p class="font-semibold text-base">{{ value }}</p>
        <p
            v-if="change !== undefined"
            :class="changeClass"
            class="text-xs mt-0.5"
        >
            {{ formatChange(change) }}
        </p>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    label: String,
    value: String,
    change: Number,
});

const changeClass = computed(() =>
    props.change >= 0 ? "text-green-500" : "text-red-500",
);

function formatChange(val) {
    const num = parseFloat(val);
    const sign = num >= 0 ? "+" : "";
    return sign + num.toFixed(2) + "%";
}
</script>
