export default defineNuxtRouteMiddleware((to, from) => {
    if (import.meta.server && !useCookie('access_token')?.value && to.path !== '/login')
        return navigateTo('/login')
})
