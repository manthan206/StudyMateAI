<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit;
}

$id = intval($_POST['id']);

$stmt = $conn->prepare("
DELETE FROM chats
WHERE id = ?
AND user_id = ?
");

$stmt->execute([
    $id,
    $_SESSION['user_id']
]);

echo "success";
?>