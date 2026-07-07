<?php
session_start();
require 'config.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT id, question, answer, created_at
    FROM chats
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$_SESSION['user_id']]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>