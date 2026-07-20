export default class ApiClient {
    constructor(config) {
        this.config = config;
        this.headers = config.headers;
        this.payload = config.payload;
        this.loadingEvent = new Event('loading');
        this.loadedEvent = new Event('loaded');
    }

    getEndpoint(action) {
        return this.config.endpoint || (this.config.endpoints || {})[action];
    }

    async fetchData(action, payload = {}) {
        document.dispatchEvent(this.loadingEvent);

        const endpoint = this.getEndpoint(action);

        try {
            if (! endpoint) {
                throw new Error(
                    `No endpoint configured for the "${action}" action. Point Paver at a ` +
                    `single endpoint with setEndpoint('/your-endpoint'), or add "${action}" ` +
                    `to setEndpoints().`
                );
            }

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...this.headers,
                },
                body: JSON.stringify({ action, ...this.payload, ...payload }),
            });

            const body = await response.text();

            if (! response.ok) {
                throw new Error(
                    `The "${action}" action endpoint (${endpoint}) responded ${response.status}. ` +
                    `Response: ${body.slice(0, 500)}`
                );
            }

            try {
                return JSON.parse(body);
            } catch (parseError) {
                throw new Error(
                    `The "${action}" action endpoint (${endpoint}) did not return JSON. ` +
                    `Response: ${body.slice(0, 500)}`
                );
            }
        } catch (error) {
            console.error(`[PAVER] ${error.message}`);
            throw error;
        } finally {
            document.dispatchEvent(this.loadedEvent);
        }
    }

    async fetchBlockOptions(block, payload = {}) {
        return this.fetchData('options', {block, ...payload});
    }

    async fetchBlock(block, payload = {}) {
        return this.fetchData('fetch', {block, ...payload});
    }

    async renderBlock(block, payload = {}) {
        return this.fetchData('render', {block, ...payload});
    }

    async resolve(className, callMethod, methodArgs, initialState = {}) {
        return this.fetchData('resolve', {
            class: className,
            call: callMethod,
            state: initialState,
            args: methodArgs
        });
    }
}
