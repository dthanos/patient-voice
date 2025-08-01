import * as querystring from "node:querystring";

export default defineEventHandler(async (event) => {
    const config = useRuntimeConfig()
    let backendUrl = `${config.backendUrl}/api/patient`

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
            'Content-Type': 'application/json'
        },
    }
    // Body included only in POST & PATCH calls
    if (['POST', 'PUT'].includes(event.method)) {
        fetchOptions.body = await readBody(event);
        fetchOptions.duplex = 'half'
    }
    else if(event.method === 'DELETE')
        delete fetchOptions.headers['content-length'];

    if(event.method === 'GET')
        backendUrl = `${backendUrl}?${querystring.encode(getQuery(event))}`;
    else if (['DELETE', 'PUT'].includes(event.method))
        backendUrl = `${backendUrl}/${getQuery(event)?.id}`;

    const response = await fetch(backendUrl, fetchOptions)
    const headers = Object.fromEntries(response.headers.entries())

    // Setting the response headers and status
    Object.entries(headers).forEach(([key, value]) => event.node.res.setHeader(key, value))
    setResponseStatus(event, response.status)

    return await response.json();
})
