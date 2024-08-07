export default class ApiClient {
    constructor(config) {
        this.config = config
        this.headers = config.headers
        this.payload = config.payload
    }

    getEndpoint(name) {
        return this.config.endpoints[name]
    }

    async fetchBlockOptions(block, payload = {}) {
        try {
            const response = await fetch(this.getEndpoint('options'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...this.headers,
                },
                body: JSON.stringify({ block, ...this.payload, ...payload }),
            })

            return await response.json()
        } catch (error) {
            console.error('Error fetching options:', error)

            throw error
        }
    }

    async fetchBlock(block, payload = {}) {
        try {
            const response = await fetch(this.getEndpoint('fetch'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...this.headers,
                },
                body: JSON.stringify({ block, ...this.payload, ...payload }),
            })

            return await response.json()
        } catch (error) {
            console.error('Error fetching block:', error)

            throw error
        }
    }

    async renderBlock(block, payload = {}) {
        try {
            const response = await fetch(this.getEndpoint('render'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...this.headers,
                },
                body: JSON.stringify({ block, ...this.payload, ...payload }),
            })

            return await response.json()
        } catch (error) {
            console.error('Error rendering block:', error)

            throw error
        }
    }
}
