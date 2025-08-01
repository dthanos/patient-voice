<template>
    <v-app>
        <v-main>
            <Topbar v-if="isAuth" />
            <NavMenu v-if="isAuth" />
            <NuxtPage />
        </v-main>
        <Snackbar />
    </v-app>
</template>
<script setup lang="ts">
import NavMenu from "./components/core/NavMenu.vue";
import Topbar from "./components/core/Topbar.vue";
import Snackbar from "./components/core/Snackbar.vue";
import {useGlobalStore} from "~/components/stores/global";

const route = useRoute();
const globalStore = useGlobalStore();
const isAuth = computed(() => !route.fullPath.includes('/login'));

onMounted(() => {
    if(route.name) {
        const routeName = route.name.includes('_') ? route.name.replace('_', ' ') : route.name;
        globalStore.title = [routeName.charAt(0).toUpperCase() + routeName.slice(1)];
    }else
        globalStore.title = ['Dashboard'];
})
</script>