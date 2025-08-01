import * as querystring from "node:querystring";

export default defineEventHandler(async (event) => {
    const config = useRuntimeConfig()
    const backendUrl = `${config.backendUrl}/api/upload?${querystring.encode(getQuery(event))}`

    // Reading cookie - extracting token
    const cookies = parseCookies(event)
    const token = cookies.access_token ?? ''

    // token existence check
    if (!token) throw createError({ statusCode: 401, statusMessage: 'Unauthorized' })

    // Constructing the request based on client request made
    const fetchOptions: RequestInit = {
        method: event.method,
        headers: {
            ...getRequestHeaders(event),
            'Authorization': `Bearer ${token}`,
        },
    }
    // Body included only in POST & PATCH calls
    if (['POST', 'PATCH'].includes(event.method)) {
        fetchOptions.body = event.node.req
        fetchOptions.duplex = 'half'
    }
    else if(event.method === 'DELETE')
        delete fetchOptions.headers['content-length'];

    const response = await fetch(backendUrl, fetchOptions)
    const headers = Object.fromEntries(response.headers.entries())

    // Setting the response headers and status
    Object.entries(headers).forEach(([key, value]) => event.node.res.setHeader(key, value))
    setResponseStatus(event, response.status)
    if(event.method === 'PATCH'){// Putting the received media ID in the response headers
        const mediaId = headers.hasOwnProperty('x-media-id') && !!headers['x-media-id']
            ? headers['x-media-id']
            : null;
        event.node.res.setHeader('x-media-id', mediaId)
    }

    const body = await response.arrayBuffer()
    return new Uint8Array(body)
})
