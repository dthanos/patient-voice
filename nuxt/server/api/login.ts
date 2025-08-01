export default defineEventHandler(async (event) => {
    const config = useRuntimeConfig()
    const backendUrl = `${config.backendUrl}/api/login`

    const response = await fetch(backendUrl, {
        method: event.method,
        headers: {
            'Content-Type': 'application/json',
        },
        duplex: 'half',
        body: event.node.req,
    })

    if (!response.ok) {// Handling error responses
        const errorText = await response.text()
        throw createError({statusCode: response.status, statusMessage: errorText || 'Login failed'})
    }

    // Extract access_token from header and setting on client via Nuxt response
    const resData = await response.json()
    const token = resData.data.access_token
    if (!token) throw createError({ statusCode: 401, statusMessage: 'Missing token.' })

    // Setting token in cookie
    setCookie(event, 'access_token', token, {
        httpOnly: true,
        secure: true,
        path: '/',
        maxAge: 60 * 60 * 24, // 1 day
    })
})
