// web/js/reports.js
import { api } from './core.js';

export async function loadReports() {
    try {
        const data = await api.cachedRequest('reports.php', 'GET', null, 'reportsCache');
        const container = document.getElementById('reportsContainer');
        if (!container) return;

        container.innerHTML = `
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Задачи</h5>
            <p class="card-text">Всего: ${data.tasks_count}</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Дела</h5>
            <p class="card-text">Всего: ${data.cases_count}</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Клиенты</h5>
            <p class="card-text">Всего: ${data.clients_count}</p>
          </div>
        </div>
      </div>
    `;
    } catch (error) {
        console.error('Reports load error:', error);
        alert('Ошибка загрузки отчётов');
    }
}