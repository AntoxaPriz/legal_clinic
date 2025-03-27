<?php
session_start();

// Проверка, что пользователь авторизован как администратор
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/database.php';
$conn = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Обработка формы для обновления настроек
    $site_name = $_POST['site_name'];
    $site_description = $_POST['site_description'];
    $privacy_policy = $_POST['privacy_policy'];

    // Обновляем настройки в базе данных
    $sql = "UPDATE settings SET value = ? WHERE name = 'site_name'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $site_name);
    $stmt->execute();

    $sql = "UPDATE settings SET value = ? WHERE name = 'site_description'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $site_description);
    $stmt->execute();

    $sql = "UPDATE settings SET value = ? WHERE name = 'privacy_policy'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $privacy_policy);
    $stmt->execute();

    // Перенаправляем на страницу настроек после обновления
    header('Location: settings.php');
    exit();
}

// Получаем текущие настройки
$sql = "SELECT * FROM settings";
$result = $conn->query($sql);
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['name']] = $row['value'];
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main>
    <h1>Настройки сайта</h1>
    <form method="POST">
        <label for="site_name">Название сайта</label>
        <input type="text" name="site_name" id="site_name" value="<?php echo $settings['site_name']; ?>"><br>

        <label for="site_description">Описание сайта</label>
        <textarea name="site_description" id="site_description"><?php echo $settings['site_description']; ?></textarea><br>

        <label for="privacy_policy">Политика конфиденциальности</label>
        <textarea name="privacy_policy" id="privacy_policy"><?php echo $settings['privacy_policy']; ?></textarea><br>

        <button type="submit">Сохранить изменения</button>
    </form>
</main>

<?php
include '../includes/footer.php';
?>
