<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SESSION["tipo"] != "aluno") {
    header("Location: ../../login.php");
    exit();
}

include("../../includes/conexao.php");

$aluno_id = (int)$_SESSION["id"];
$pagamento_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if (!$pagamento_id) {
    header("Location: caixa.php");
    exit();
}

// Busca o pagamento e verifica que pertence ao aluno
$sql = "SELECT p.*, u.nome, u.email FROM pagamentos p
        INNER JOIN usuarios u ON u.id = p.aluno_id
        WHERE p.id = $pagamento_id AND p.aluno_id = $aluno_id AND p.status != 'pago'";
$resultado = $conexao->query($sql);

if (!$resultado || $resultado->num_rows === 0) {
    header("Location: caixa.php");
    exit();
}

$pagamento = $resultado->fetch_assoc();

// Access Token do MercadoPago (TESTE)
$access_token = "APP_USR-196857501195076-053013-eb345ffe4ff284058772344e4141824d-3438542610";

// URL base do seu projeto
$base_url = "http://localhost/GymFlow-main";

// Cria a preferência de pagamento
$preference_data = [
    "items" => [
        [
            "title" => "Mensalidade GymFlow - " . date("m/Y", strtotime($pagamento["vencimento"])),
            "quantity" => 1,
            "currency_id" => "BRL",
            "unit_price" => (float)$pagamento["valor"]
        ]
    ],
    "payer" => [
        "name" => $pagamento["nome"],
        "email" => $pagamento["email"]
    ],
    "back_urls" => [
        "success" => "http://localhost/GymFlow-main/paginas/aluno/retorno-pagamento.php?id=$pagamento_id&status=sucesso",
        "failure" => "http://localhost/GymFlow-main/paginas/aluno/retorno-pagamento.php?id=$pagamento_id&status=falha",
        "pending" => "http://localhost/GymFlow-main/paginas/aluno/retorno-pagamento.php?id=$pagamento_id&status=pendente"
    ],
    "external_reference" => (string)$pagamento_id,
    "payment_methods" => [
        "excluded_payment_types" => [],
        "excluded_payment_methods" => [],
        "installments" => 1,
        "default_payment_method_id" => null
    ],
];

$ch = curl_init("https://api.mercadopago.com/checkout/preferences");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($preference_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access_token"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

if ($http_code === 201 && isset($data["init_point"])) {
    // Redireciona para o checkout do MercadoPago (produção)
    // Para testes use: $data["sandbox_init_point"]
    header("Location: " . $data["sandbox_init_point"]);
    exit();
} else {
    echo "<pre>HTTP Code: $http_code\nResposta: $response</pre>";
    exit();
}
