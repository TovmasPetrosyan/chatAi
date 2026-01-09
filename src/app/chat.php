<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}

$apiKey = getenv('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');
if (!$apiKey) {
  http_response_code(500);
  echo json_encode(['error' => 'Missing OPENAI_API_KEY']);
  exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$userMessage = trim((string)($data['message'] ?? ''));
if ($userMessage === '') {
  http_response_code(400);
  echo json_encode(['error' => 'Message is required']);
  exit;
}

$payload = [

  'model' => 'gpt-4.1-mini',
  'input' => [
    [
      'role' => 'user',
      'content' => [
        ['type' => 'input_text', 'text' => $userMessage]
      ]
    ]
  ],
];

$ch = curl_init('https://api.openai.com/v1/responses');
curl_setopt_array($ch, [
  CURLOPT_POST => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
  ],
  CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
  CURLOPT_TIMEOUT => 30,
]);

$result = curl_exec($ch);
$errno = curl_errno($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($errno) {
  http_response_code(502);
  echo json_encode(['error' => 'Upstream request failed']);
  exit;
}

$decoded = json_decode($result, true);


if ($httpCode >= 400) {
  http_response_code($httpCode);
  echo json_encode([
    'error' => $decoded['error']['message'] ?? 'OpenAI error',
    'raw' => $decoded
  ], JSON_UNESCAPED_UNICODE);
  exit;
}


$text = $decoded['output_text'] ?? null;


if ($text === null && isset($decoded['output'][0]['content'][0]['text'])) {
  $text = $decoded['output'][0]['content'][0]['text'];
}

echo json_encode([
  'answer' => $text ?? '',
  'raw' => $decoded
], JSON_UNESCAPED_UNICODE);
