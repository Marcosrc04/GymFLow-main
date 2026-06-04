<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"])) {
    http_response_code(401);
    echo json_encode(["erro" => "Não autorizado"]);
    exit();
}

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';

if (empty($prompt)) {
    echo json_encode(["erro" => "Prompt vazio"]);
    exit();
}

$env = parse_ini_file(__DIR__ . '/.env');

if (!$env || !isset($env['GROQ_API_KEY'])) {
    echo json_encode(["erro" => "Chave da API não configurada"]);
    exit();
}

$groq_key = $env['GROQ_API_KEY'];

$payload = [
    "model" => "llama-3.3-70b-versatile",
    "messages" => [
        [
            "role" => "system",
            "content" => "Você é um personal trainer especialista em treinos personalizados. Responda sempre em português brasileiro."
        ],
        [
            "role" => "user",
            "content" => $prompt
        ]
    ],
    "max_tokens" => 1500,
    "temperature" => 0.7
];

$ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $groq_key"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['choices'][0]['message']['content'])) {
    echo json_encode(["texto" => $data['choices'][0]['message']['content']]);
} else {
    echo json_encode(["erro" => "Erro na API", "detalhes" => $data]);
}
?>