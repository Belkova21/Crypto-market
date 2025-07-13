import {
    createMemoryHistory,
    createRouter,
    createWebHistory,
} from "vue-router";


const routes = [
    {
        path: "/",
        name: "home",
        component: () => import("../pages/MainPage.vue"),
    }
];

export const router = createRouter({
    history: createWebHistory("/"),
    routes,
});
