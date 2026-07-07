<?php
session_start();
require 'config.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid Request"
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$chat_id = isset($input["chat_id"]) ? intval($input["chat_id"]) : 0;
$rating = isset($input["rating"]) ? trim($input["rating"]) : "";

if ($chat_id <= 0 || !in_array($rating, ["like", "dislike"])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid Data"
    ]);
    exit;
}

// Verify that the chat belongs to the logged-in user
$check = $conn->prepare("
    SELECT id
    FROM chats
    WHERE id = ?
    AND user_id = ?
");

$check->execute([$chat_id, $_SESSION['user_id']]);

if ($check->rowCount() == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Chat not found"
    ]);
    exit;
}

// Check if feedback already exists
$exists = $conn->prepare("
    SELECT id
    FROM feedback
    WHERE chat_id = ?
");

$exists->execute([$chat_id]);

if ($exists->rowCount() > 0) {

    $update = $conn->prepare("
        UPDATE feedback
        SET rating = ?
        WHERE chat_id = ?
    ");

    $update->execute([$rating, $chat_id]);

} else {

    $insert = $conn->prepare("
        INSERT INTO feedback(chat_id, rating)
        VALUES(?, ?)
    ");

    $insert->execute([$chat_id, $rating]);
}

echo json_encode([
    "success" => true,
    "message" => "Feedback Saved Successfully"
]);
?>