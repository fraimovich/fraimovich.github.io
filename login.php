<?php
session_start();
header('Content-Type: application/json');

// ---------- НАСТРОЙКИ ПОДКЛЮЧЕНИЯ (замените пароль) ----------
$host = 'orb7706769.mysql';
$user = 'orb7706769_mysql';
$password = 'vCRu5qo/';   // ← обязательно поменять!
$dbname = 'orb7706769_db';
// ---------------------------------------------------------------

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к БД: ' . $conn->connect_error]);
    exit;
}

$login = trim($_POST['login'] ?? '');
$pass = trim($_POST['pass'] ?? '');

if (empty($login) || empty($pass)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit;
}

$stmt = $conn->prepare("SELECT id, pass FROM clients WHERE login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $hash);
    $stmt->fetch();
    if (password_verify($pass, $hash)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['login'] = $login;
        echo json_encode(['success' => true, 'message' => 'Вход выполнен', 'redirect' => '/dashboard.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Неверный пароль']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Пользователь не найден']);
}
$stmt->close();
$conn->close();
?>

