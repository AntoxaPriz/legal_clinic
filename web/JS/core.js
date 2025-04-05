// web/js/core.js
export const api = {
    async request(url, method = 'GET', data = null) {
        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        };
        const options = { method, headers };
        if (data) options.body = JSON.stringify(data);

        const response = await fetch(`api/${url}`, options);
        const result = await response.json();

        if (!navigator.onLine && !response.ok) {
            // Очередь для оффлайн-режима
            await navigator.serviceWorker.ready;
            navigator.serviceWorker.controller.postMessage({ type: 'queue', url, method, data });
            if ('SyncManager' in window) {
                await navigator.serviceWorker.ready.then(reg => reg.sync.register('sync-queued-requests'));
            }
            return { success: true, queued: true, message: 'Действие сохранено и будет выполнено при подключении' };
        }

        if (!response.ok) throw new Error(result.message || 'Ошибка запроса');
        return result;
    },

    async cachedRequest(url, method = 'GET', data = null, cacheKey) {
        if (navigator.onLine) {
            const response = await this.request(url, method, data);
            localStorage.setItem(cacheKey, JSON.stringify(response));
            return response;
        }
        const cached = localStorage.getItem(cacheKey);
        return cached ? JSON.parse(cached) : [];
    }
};