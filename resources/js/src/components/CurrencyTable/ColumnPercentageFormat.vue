<template>
    <div :class="value >= 0 ? 'up' : 'down'">
        <v-icon size="14" class="me-1">
            {{ value >= 0 ? "mdi-arrow-up" : "mdi-arrow-down" }}
        </v-icon>
        {{ value !== null ? Math.abs(value).toFixed(2) + "%" : "-" }}
    </div>
</template>

<script setup>
defineProps({ value: Number });
</script>

<style>
.up {
    color: #16c784;
}
.down {
    color: #ea3943;
}
</style>
