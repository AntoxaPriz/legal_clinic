<?php include 'includes/header.php'; ?>

<main>
    <h1>Подача заявки</h1>
    <form action="submit_request.php" method="POST">
        <label for="title">Тема заявки:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="description">Описание:</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="category">Категория:</label>
        <select id="category" name="category">
            <option value="legal_help">Юридическая помощь</option>
            <option value="document_drafting">Составление документов</option>
            <option value="court_representation">Представительство в суде</option>
        </select><br>

        <button type="submit">Отправить заявку</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
