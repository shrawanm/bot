<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // your React dev server
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Read JSON body instead of $_POST
    $input = json_decode(file_get_contents('php://input'), true);
    $userMessage = trim($input['message'] ?? '');

    $apiKey = 'AIzaSyDa-Vs9x9AofpFapg7NtBjobOhPP6Pi5uQ';

    if (empty($userMessage)) {
        echo json_encode(['reply' => 'Please enter a message.']);
        exit;
    }

    // Gemini prompt (general chatbot, no restrictions)
    $prompt = "You are a helpful AI chatbot. Answer the user's messages naturally.\nUser said: \"$userMessage\"";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

    $postData = json_encode([
        'contents' => [[
            'parts' => [['text' => $prompt]]
        ]]
    ]);

    $headers = [
        "Content-Type: application/json",
        "X-goog-api-key: $apiKey"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo json_encode(['reply' => '❌ Server error. Check PHP/XAMPP setup.']);
        exit;
    }

    $responseData = json_decode($response, true);
    $botReply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '❌ Server error. No response from Gemini.';

    echo json_encode(['reply' => $botReply]);
}
?>
