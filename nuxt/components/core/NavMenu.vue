<template>
    <v-navigation-drawer v-model="navMenu">
        <v-list density="compact" nav color="primary" v-model:selected="title">
            <v-list-item
                prepend-icon="mdi-home-outline"
                :active="!!title?.length && title[0] === 'Dashboard'"
                :value="'Dashboard'"
                class="d-flex align-center"
                title="Dashboard"
                :to="`dashboard`"
            />
            <v-list-item
                prepend-icon="mdi-account-group-outline"
                :active="!!title?.length && title[0] === 'Patients'"
                :value="'Patients'"
                class="d-flex align-center"
                title="Patients"
                :to="`patients`"
            />
            <v-list-item
                prepend-icon="mdi-microphone-settings"
                :active="!!title?.length && title[0] === 'Voice analysis'"
                :value="'Voice analysis'"
                class="d-flex align-center"
                title="Voice analysis"
                :to="`voice_analysis`"
            />
        </v-list>
    </v-navigation-drawer>
</template>

<script setup>
import {storeToRefs} from "pinia";
import {useGlobalStore} from "~/components/stores/global.ts";

const globalStore = useGlobalStore()
const { navMenu, title } = storeToRefs(globalStore)


</script>
