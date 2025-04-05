// web/js/core.js
export const api = {
    async request(endpoint, method = 'GET', body = null) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        };
        if (body) options.body = JSON.stringify(body);

        const response = await fetch(`api/${endpoint}`, options);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    },

    // Новая функция для кэширования
    async cachedRequest(endpoint, method = 'GET', body = null, cacheKey, ttl = 3600000) { // 1 час TTL
        const cache = JSON.parse(localStorage.getItem(cacheKey) || '{}');
        const now = Date.now();

        if (cache.data && cache.timestamp && (now - cache.timestamp < ttl)) {
            return cache.data; // Возвращаем кэшированные данные
        }

        const data = await this.request(endpoint, method, body);
        localStorage.setItem(cacheKey, JSON.stringify({ data, timestamp: now }));
        return data;
    }
};

export function clearCache() {
    localStorage.clear(); // Очистка всего кэша (можно уточнить по ключам)
}