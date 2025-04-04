// web/js/core.js
class APIClient {
    constructor() {
        this.socket = null;
        this.socketListeners = new Map();
        this.config = {
            apiBaseUrl: '/api/', // Пустая строка, так как PHP-файлы в корне web/
            wsUrl: `wss://${window.location.host}/ws`,
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || ''
        };
    }

    async request(endpoint, method = 'GET', data = null) {
        const url = `${this.config.apiBaseUrl}${endpoint}`;
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': this.config.csrfToken,
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            credentials: 'include'
        };

        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);
        const responseData = await response.json();

        if (!response.ok) {
            throw new Error(responseData.message || `HTTP error! status: ${response.status}`);
        }

        return responseData;
    }

    connectWebSocket() {
        if (this.socket) return;

        this.socket = new WebSocket(this.config.wsUrl);

        this.socket.onopen = () => {
            console.log('WebSocket connected');
            this.socket.send(JSON.stringify({
                type: 'auth',
                token: localStorage.getItem('token')
            }));
        };

        this.socket.onmessage = (event) => {
            const message = JSON.parse(event.data);
            const listeners = this.socketListeners.get(message.type) || [];
            listeners.forEach(callback => callback(message.data));
        };

        this.socket.onclose = () => {
            console.log('WebSocket disconnected');
            setTimeout(() => this.connectWebSocket(), 5000);
        };
    }

    subscribe(eventType, callback) {
        if (!this.socketListeners.has(eventType)) {
            this.socketListeners.set(eventType, []);
        }
        this.socketListeners.get(eventType).push(callback);
        return () => this.unsubscribe(eventType, callback);
    }

    unsubscribe(eventType, callback) {
        const listeners = this.socketListeners.get(eventType);
        if (listeners) {
            this.socketListeners.set(eventType,
                listeners.filter(listener => listener !== callback));
        }
    }
}

export const api = new APIClient();