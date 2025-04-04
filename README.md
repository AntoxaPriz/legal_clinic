# Legal Clinic CRM

Система электронного документооборота для юридической клиники ЧОУ ВО «Московский университет имени С.Ю. Витте».

## Установка
1. Создайте БД MySQL и выполните `db_setup.sql`.
2. Разместите проект на сервере с PHP и MySQL.
3. Настройте доступ к папке `uploads/` для записи.

## Использование
- Авторизация: `login.html` (client/789, student/456, curator/123).
- Загрузка документов: `index.html`.
- Управление: `admin.html`, `profile.html`.

## Технологии
- Frontend: HTML, CSS, JavaScript.
- Backend: PHP, MySQL.
- OCR: Python (FastAPI).
