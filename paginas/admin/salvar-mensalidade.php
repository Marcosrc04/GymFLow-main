<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SESSION["tipo"] != "admin") {
    header("Location: ../../login.php");
    exit();
}

include("../../includes/conexao.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: pagamentos.php");
    exit();
}

$aluno_id = isset($_POST["aluno_id"]) ? (int)$_POST["aluno_id"] : 0;
$valor = isset($_POST["valor"]) ? (float)$_POST["valor"] : 0;
$vencimento = isset($_POST["vencimento"]) ? trim($_POST["vencimento"]) : "";

if ($aluno_id <= 0 || $valor <= 0 || empty($vencimento)) {
    die("Dados inválidos.");
}

$sql_verifica = "SELECT id FROM usuarios WHERE id = ? AND tipo = 'aluno'";
$stmt_verifica = $conexao->prepare($sql_verifica);
$stmt_verifica->bind_param("i", $aluno_id);
$stmt_verifica->execute();
$resultado_verifica = $stmt_verifica->get_result();

if ($resultado_verifica->num_rows === 0) {
    die("Aluno inválido.");
}

$sql = "INSERT INTO pagamentos (aluno_id, valor, vencimento, data_pagamento, status)
        VALUES (?, ?, ?, NULL, 'pendente')";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("ids", $aluno_id, $valor, $vencimento);

if ($stmt->execute()) {
    header("Location: pagamentos.php");
    exit();
} else {
    die("Erro ao salvar mensalidade: " . $conexao->error);
}