// https://nuxt.com/docs/api/configuration/nuxt-config
import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify'
export default defineNuxtConfig({
    ssr: true,
    compatibilityDate: '2025-05-15',
    devtools: { enabled: true },
    build: {
        transpile: ['vuetify'],
    },
    modules: [
        (_options: any, nuxt: any) => {
            nuxt.hooks.hook('vite:extendConfig', (config: any) => {
                config.plugins.push(vuetify({ autoImport: true }))
            })
        },
        '@pinia/nuxt',
    ],
    vite: {
        vue: {
            template: {
                transformAssetUrls,
            },
        },
    },
    runtimeConfig: {
        backendUrl: process.env.BACKEND_URL || '',
    }
})
