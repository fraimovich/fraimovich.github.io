<?php
header('Content-Type: application/json');

// ---------- НАСТРОЙКИ ПОДКЛЮЧЕНИЯ (замените пароль) ----------
$host = 'localhost';  //$host = 'orb7706769.mysql';
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
$email = trim($_POST['email'] ?? '');
$pass = trim($_POST['pass'] ?? '');

if (empty($login) || empty($email) || empty($pass)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Некорректный email']);
    exit;
}
if (strlen($pass) < 6) {
    echo json_encode(['success' => false, 'message' => 'Пароль должен быть не менее 6 символов']);
    exit;
}

// Проверка уникальности логина и email
$check = $conn->prepare("SELECT id FROM clients WHERE login = ? OR email = ?");
$check->bind_param("ss", $login, $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Логин или email уже заняты']);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO clients (login, email, pass) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $login, $email, $hash);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Регистрация успешна! Теперь войдите']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при регистрации: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
?>
