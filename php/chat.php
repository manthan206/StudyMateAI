<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

header('Content-Type: application/json');

require 'config.php';
require 'groq_config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input["message"])) {
    echo json_encode([
        "success" => false,
        "message" => "No message received"
    ]);
    exit;
}

$userMessage = trim($input["message"]);

if ($userMessage == "") {
    echo json_encode([
        "success" => false,
        "message" => "Empty message"
    ]);
    exit;
}

$systemPrompt = "You are StudyMateAI, an educational AI assistant.

Rules:
- Answer only educational questions.
- Explain clearly.
- Show steps for mathematics.
- Write clean code for programming.
- Use bullet points when helpful.
- If asked a non-educational question, politely answer briefly and redirect toward learning.";

$data = [
    "model" => GROQ_MODEL,
    "messages" => [
        [
            "role" => "system",
            "content" => $systemPrompt
        ],
        [
            "role" => "user",
            "content" => $userMessage
        ]
    ],
    "temperature" => 0.4,
    "max_tokens" => 1024
];

$ch = curl_init("https://api.groq.com/openai/v1/chat/completions");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . GROQ_API_KEY,
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        "success" => false,
        "message" => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$result = json_decode($response, true);

if (!isset($result["choices"][0]["message"]["content"])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid API response",
        "response" => $result
    ]);
    exit;
}

//  CLEAN AND CORRECT CODE:
$answer = $result["choices"][0]["message"]["content"];

try {
    $save = $conn->prepare("
        INSERT INTO chats (user_id, question, answer)
        VALUES (?, ?, ?)
    ");

    $save->execute([
        $_SESSION['user_id'],
        $userMessage,
        $answer
    ]);

    $chatId = $conn->lastInsertId();

    // Send the final clean JSON back to JavaScript
    echo json_encode([
        "success" => true,
        "answer" => $answer,
        "chat_id" => $chatId
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}