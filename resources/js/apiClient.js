export default class ApiClient {
    constructor(config) {
        this.config = config;
        this.headers = config.headers;
        this.payload = config.payload;
        this.loadingEvent = new Event('loading');
        this.loadedEvent = new Event('loaded');
    }

    getEndpoint(name) {
        return this.config.endpoints[name];
    }

    async fetchData(endpointName, payload = {}) {
        document.dispatchEvent(this.loadingEvent);

        try {
            const response = await fetch(this.getEndpoint(endpointName), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...this.headers,
                },
                body: JSON.stringify({ ...this.payload, ...payload }),
            });

            document.dispatchEvent(this.loadedEvent);

            return await response.json();
        } catch (error) {
            document.dispatchEvent(this.loadedEvent);
            console.error(`Error ${endpointName} block:`, error);
            throw error;
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
