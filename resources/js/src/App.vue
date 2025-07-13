<template>
    <v-app>
        <div v-if="isOffline" class="offline-banner">Offline</div>
        <RouterView />
    </v-app>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from "vue";

const isOffline = ref(true);
let socketCheckInterval = null;

const getConnectionState = () => {
    return (
        window.Echo?.connector?.pusher?.connection?.state ||
        window.Echo?.connector?.connection?.state ||
        null
    );
};

onMounted(() => {
    socketCheckInterval = setInterval(() => {
        const state = getConnectionState();
        isOffline.value = state !== "connected";
    }, 1000);
});

onUnmounted(() => {
    if (socketCheckInterval) clearInterval(socketCheckInterval);
});
</script>

<style scoped>
.offline-banner {
    background-color: oklch(0.7 0.21 22.57);
    color: white;
    padding: 12px;
    text-align: center;
    font-weight: bold;
    font-size: 0.95rem;
}
</style>
