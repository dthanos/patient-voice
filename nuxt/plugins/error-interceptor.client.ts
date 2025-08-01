import {useSnackbarStore} from "~/components/stores/snackbar";

export default defineNuxtPlugin(() => {
    const snackbarStore = useSnackbarStore()
    const originalFetch = window.fetch.bind(window)

    window.fetch = (async (input: RequestInfo | URL, init?: RequestInit) => {
        try {
            const response = await originalFetch(input, init)
            if (!response.ok) {
                const contentType = response.headers.get('content-type') || ''
                const errorData = contentType.includes('application/json')
                    ? await response.clone().json()
                    : await response.clone().text();
                snackbarStore.addSnackbar({variant: 'red', text: `Error: ${errorData.message ?? 'Server error'}`})
            }

            return response
        } catch (err) {
            throw err;
        }
    }) as typeof fetch
})
