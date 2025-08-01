import * as querystring from "node:querystring";

export default defineEventHandler(async (event) => {
    const config = useRuntimeConfig()
    const backendUrl = `${config.backendUrl}/api/logout`

    // Reading cookie - extracting token
    const cookies = parseCookies(event)
    const token = cookies.access_token ?? ''

    // token existence check
    if (!token) throw createError({ statusCode: 401, statusMessage: 'Unauthorized' })

    // Constructing the request based on client request made
    const response = await fetch(backendUrl, {
        method: event.method,
        headers: {
            'Authorization': `Bearer ${token}`,
        },
        body: {},
        duplex: 'half',
    });

    deleteCookie(event, 'access_token');
})
