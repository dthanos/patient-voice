<template>
    <div>
        <v-snackbar
            v-for="(snackbar, index) in snackbarStore.snackbars"
            location="right top"
            :key="index"
            :variant="'elevated'"
            :timeout="snackbar.variant === 'error' ? -1 : snackbar.timeout"
            :color="snackbar.variant"
            :model-value="true"
        >
            <div class="d-flex align-center justify-space-between">
                <div>
                    <v-icon
                        class="pr-1"
                        v-if="snackbar.variant === 'success'"
                        :icon="'mdi-check-circle-outline'"
                    />
                    <v-icon
                        class="pr-1"
                        v-else-if="snackbar.variant === 'error'"
                        :icon="'mdi-alpha-x-circle-outline'"
                    />
                    <v-icon
                        class="pr-1"
                        v-else-if="snackbar.variant === 'warning'"
                        :icon="'mdi-alert-circle-outline'"
                    />
                    {{snackbar.text}}
                </div>
            </div>
            <template v-slot:actions="{ isActive }">
                <v-icon
                    v-if="snackbar.variant === 'error'"
                    :icon="'mdi-close-circle-outline'"
                    @click="isActive.value = false"
                />
            </template>
        </v-snackbar>
    </div>
</template>

<script setup>
import {useSnackbarStore} from "~/components/stores/snackbar";
const snackbarStore = useSnackbarStore()
</script>
