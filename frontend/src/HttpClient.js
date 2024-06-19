import axios from 'axios';

class HttpClient {
    baseUrl = import.meta.env.VITE_APP_API_URL;

    constructor() {

        this.client = axios.create({
            baseURL: this.baseUrl,
        });
    }

    get(url, config = {}) {
        return this.client.get(url, config);
    }

    post(url, data = {}, config = {}) {
        return this.client.post(url, data, config);
    }

    put(url, data = {}, config = {}) {
        return this.client.put(url, data, config);
    }

    delete(url, config = {}) {
        return this.client.delete(url, config);
    }
}

export default HttpClient;
