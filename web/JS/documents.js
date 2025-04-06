// web/js/documents.js
import { api } from './core.js';

export async function loadDocuments() {
    const tableBody = document.querySelector('#documentsList tbody');
    if (!tableBody) return;

    tableBody.innerHTML = '<tr><td colspan="3" class="text-center"><span class="spinner-border spinner-border-sm"></span> Загрузка...</td></tr>';

    try {
        const data = await api.cachedRequest('documents.php', 'GET', null, 'documentsCache');
        tableBody.innerHTML = '';
        data.forEach(doc => {
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${doc.id}</td>
        <td>${doc.title}</td>
        <td>
          <button class="btn btn-primary btn-sm ocr-doc" data-path="${doc.file_path}" data-id="${doc.id}">Распознать текст</button>
        </td>
      `;
            row.querySelector('.ocr-doc').addEventListener('click', () => recognizeDocument(doc.file_path, doc.id));
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error('Documents load error:', error);
        tableBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Ошибка загрузки документов</td></tr>';
    }
}

async function recognizeDocument(filePath, documentId) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Распознавание текста</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="display: flex;">
          <div style="width: 70%; padding: 10px;">
            <img id="previewImage" src="${filePath}" style="max-width: 100%; height: auto;" alt="Предпросмотр">
          </div>
          <div style="width: 30%; padding: 10px;">
            <label for="psmSelect">PSM:</label>
            <select id="psmSelect" class="form-select mb-2">
              ${Array.from({ length: 14 }, (_, i) => `<option value="${i}" ${i === 6 ? 'selected' : ''}>${i}</option>`).join('')}
            </select>
            <textarea id="ocrText" class="form-control" rows="10" placeholder="Распознанный текст появится здесь"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" id="runOcr">Распознать</button>
          <button class="btn btn-success" id="saveToDb">Сохранить в базу</button>
          <button class="btn btn-secondary" id="saveTxt">Сохранить в TXT</button>
          <button class="btn btn-secondary" id="saveDocx">Сохранить в DOCX</button>
          <button class="btn btn-secondary" id="savePdf">Сохранить в PDF</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  `;
    document.body.appendChild(modal);

    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    modal.querySelector('#runOcr').addEventListener('click', async () => {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.onchange = async () => {
            const file = fileInput.files[0];
            const formData = new FormData();
            formData.append('image', file);
            formData.append('psm', modal.querySelector('#psmSelect').value);

            try {
                const response = await fetch('api/ocr.php', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    modal.querySelector('#ocrText').value = data.text;
                    modal.querySelector('#previewImage').src = data.filePath;
                    documentId = data.document_id; // Обновляем documentId после успешного распознавания
                } else {
                    modal.querySelector('#ocrText').value = 'Ошибка: ' + data.message;
                }
            } catch (error) {
                console.error('OCR error:', error);
                modal.querySelector('#ocrText').value = 'Ошибка распознавания';
            }
        };
        fileInput.click();
    });

    modal.querySelector('#saveToDb').addEventListener('click', async () => {
        const text = modal.querySelector('#ocrText').value;
        if (!documentId) {
            alert('Сначала выполните распознавание текста');
            return;
        }
        try {
            const response = await fetch('api/update_ocr.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ document_id: documentId, extracted_text: text })
            });
            const data = await response.json();
            alert(data.success ? 'Текст сохранён в базе' : 'Ошибка: ' + data.message);
        } catch (error) {
            console.error('Save error:', error);
            alert('Ошибка сохранения');
        }
    });

    // Сохранение в TXT
    modal.querySelector('#saveTxt').addEventListener('click', () => {
        const text = modal.querySelector('#ocrText').value;
        if (text) {
            const blob = new Blob([text], { type: 'text/plain' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'ocr_result.txt';
            link.click();
        }
    });

    // Сохранение в DOCX
    modal.querySelector('#saveDocx').addEventListener('click', () => {
        const text = modal.querySelector('#ocrText').value;
        if (text) {
            const html = `<html><body><p>${text.replace(/\n/g, '</p><p>')}</p></body></html>`;
            const blob = new Blob([html], { type: 'application/msword' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'ocr_result.docx';
            link.click();
        }
    });

    // Сохранение в PDF
    modal.querySelector('#savePdf').addEventListener('click', () => {
        const text = modal.querySelector('#ocrText').value;
        if (text) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.text(text, 10, 10);
            doc.save('ocr_result.pdf');
        }
    });

    modal.addEventListener('hidden.bs.modal', () => modal.remove());
}