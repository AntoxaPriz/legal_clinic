// web/js/help.js
import { api } from './core.js';

export async function loadHelp() {
    try {
        const data = await api.cachedRequest('help.php', 'GET', null, 'helpCache');
        const container = document.querySelector('.container');
        if (!container) return;

        container.innerHTML = `
      <h2>Помощь</h2>
      <div class="accordion" id="faqAccordion">
        ${data.faq.map((item, index) => `
          <div class="accordion-item">
            <h2 class="accordion-header" id="heading${index}">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="true" aria-controls="collapse${index}">
                ${item.question}
              </button>
            </h2>
            <div id="collapse${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="heading${index}" data-bs-parent="#faqAccordion">
              <div class="accordion-body">${item.answer}</div>
            </div>
          </div>
        `).join('')}
      </div>
    `;
    } catch (error) {
        console.error('Help load error:', error);
        alert('Ошибка загрузки помощи');
    }
}