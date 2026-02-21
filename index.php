<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$client = new Client([
    'base_uri' => 'https://api.groq.com',
    'timeout'  => 30.0,
]);

function getSessionDir() {
    $dir = sys_get_temp_dir() . '/alice_sessions';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    return $dir;
}

function loadHistory($sessionId) {
    $file = getSessionDir() . '/' . md5($sessionId) . '.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (is_array($data)) return $data;
    }
    return [];
}

function saveHistory($sessionId, $history) {
    $file = getSessionDir() . '/' . md5($sessionId) . '.json';
    $history = array_slice($history, -20);
    file_put_contents($file, json_encode($history, JSON_UNESCAPED_UNICODE));
}

function cleanRequest($request) {
    $cutWords = ['Алиса', 'алиса'];
    foreach ($cutWords as $word) {
        if (mb_stripos($request, $word) === 0) {
            $request = mb_substr($request, mb_strlen($word));
        }
    }
    return trim($request);
}

function askDeepSeek($history, $client) {
    $apiKey = getenv('DEEPSEEK_API_KEY');
    if (empty($apiKey)) {
        return 'API ключ не найден.';
    }

    $messages = [
        [
            "role" => "system",
            "content" => "Отвечай кратко и по существу. Максимум 500 символов. Ты голосовой помощник, говори простым языком."
        ]
    ];
    foreach ($history as $msg) {
        $messages[] = $msg;
    }

    try {
        $response = $client->post('/openai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'model'    => 'llama-3.3-70b-versatile',
                'messages' => $messages,
                'stream'   => false
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        return trim($body['choices'][0]['message']['content']);
    } catch (Exception $e) {
        error_log($e->getMessage());
        return 'Не удалось получить ответ от сервиса.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $response = [
        'session'  => $input['session'],
        'version'  => $input['version'],
        'response' => [
            'end_session' => false
        ]
    ];

    $sessionId = $input['session']['session_id'];
    $history = loadHistory($sessionId);

    if (!empty($input['request']['original_utterance'])) {
        $userMessage = cleanRequest($input['request']['original_utterance']);
        $history[] = ['role' => 'user', 'content' => $userMessage];

        $botReply = askDeepSeek($history, $client);
        $botReply = mb_substr($botReply, 0, 900);

        $history[] = ['role' => 'assistant', 'content' => $botReply];
        saveHistory($sessionId, $history);

        $response['response']['text'] = $botReply;
        $response['response']['tts'] = $botReply . '<speaker audio="alice-sounds-things-door-2.opus">';
    } else {
        $response['response']['text'] = 'Я умный чат-бот. Спроси что-нибудь.';
        $response['response']['tts'] = 'Я умный чат-бот. Спроси что-нибудь.';
    }

    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Метод не поддерживается.";
}
