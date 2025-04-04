<?php
function uploadFile($file)
{
    $targetDir = "../uploads/";
    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Проверка на тип файла
    if ($file["size"] > 500000) {
        echo "Файл слишком большой.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Извините, файл не был загружен.";
    } else {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            echo "Файл " . basename($file["name"]) . " был загружен.";
        } else {
            echo "Произошла ошибка при загрузке файла.";
        }
    }
}
?>
