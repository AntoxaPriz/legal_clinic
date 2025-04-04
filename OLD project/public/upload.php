<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    require_once '../src/helpers/FileHelper.php';
    uploadFile($_FILES['document']);
}
?>

<form method="POST" enctype="multipart/form-data">
    Выберите файл для загрузки: <input type="file" name="document" required>
    <button type="submit">Загрузить</button>
</form>
