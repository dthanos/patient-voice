<template>
    <v-app-bar prominent density="compact" color="primary">
        <v-app-bar-nav-icon @click="navMenu = !navMenu" />

        <v-toolbar-title :text="title" />

        <v-spacer></v-spacer>

        <v-menu offset-y>
            <template #activator="{ props }">
                <v-btn icon v-bind="props">
                    <v-icon icon="mdi-dots-vertical" />
                </v-btn>
            </template>

            <v-list>
                <v-list-item @click="logout">
                    <v-list-item-title>
                        Logout
                        <v-icon color="error" icon="mdi-logout" class="ml-2" />
                    </v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>
    </v-app-bar>
</template>

<script setup>
import {useGlobalStore} from "~/components/stores/global.ts";
import {storeToRefs} from "pinia";

const router = useRouter()
const globalStore = useGlobalStore()
const { navMenu, title } = storeToRefs(globalStore)

function logout(){
    fetch('/api/logout', {
        method: 'POST',
        body: JSON.stringify({}),
    })
        .then(res => router.push('/login'))
}
</script>
