<template>
    <v-card class="pt-5 h-100 d-flex align-center justify-center w-100" flat>
        <div ref="uppyContainer" class="w-100" />
    </v-card>
</template>

<script setup lang="ts">
import { onMounted, ref, nextTick, onUnmounted } from 'vue'
import Uppy from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import Tus from '@uppy/tus'
import '@uppy/core/dist/style.css'
import '@uppy/dashboard/dist/style.css'
import {useRouter} from "vue-router";

const router = useRouter()
const emit = defineEmits(['uploaded', 'reset'])
const uppyContainer = ref(null)
let uppy: Uppy.Uppy


onMounted(() => {
    uppy = new Uppy({
        autoProceed: false,
        restrictions: {
            allowedFileTypes: ['audio/*'],// File restriction
            maxNumberOfFiles: 1,
            maxFileSize: 200 * 1024 * 1024,
        },
    })
        .use(Dashboard, {
            inline: true,
            target: uppyContainer.value,
            showProgressDetails: true,
            height: 400,
            width: "100%",
            locale: {
                strings: {
                    done: 'Reset',
                }
            }
        })
        .use(Tus, {
            endpoint: '/api/upload',
            chunkSize: 1024 * 1024,
            retryDelays: [0, 1000, 3000, 5000],
        })
        .on('upload-success', async (file: any, response: any) => {
            const xhr = response.body.xhr
            const mediaId = xhr?.getResponseHeader('x-media-id')
            setTimeout(document.querySelector('.uppy-StatusBar-actionBtn--done')?.addEventListener('click', () => emit('reset')), 500)
            emit('uploaded', mediaId);
        })


})
onUnmounted(() => {})
</script>
