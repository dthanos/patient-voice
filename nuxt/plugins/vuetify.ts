import '@mdi/font/css/materialdesignicons.css'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'

export default defineNuxtPlugin((app) => {
    const vuetify = createVuetify({
        ssr: true,
        components,
        directives,
        defaults: {
            VTextField: {
                color: 'primary',
                density: 'comfortable',
            },
        }
    })
    app.vueApp.use(vuetify)
})
