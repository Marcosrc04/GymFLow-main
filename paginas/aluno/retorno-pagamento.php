<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"])) {
    header("Location: ../../login.php");
    exit();
}

include("../../includes/conexao.php");

date_default_timezone_set("America/Sao_Paulo");

$pagamento_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$status = isset($_GET["status"]) ? $_GET["status"] : "";
$mp_payment_id = isset($_GET["payment_id"]) ? $_GET["payment_id"] : null;
$mp_status = isset($_GET["collection_status"]) ? $_GET["collection_status"] : null;
$metodo = isset($_GET["payment_type"]) ? $_GET["payment_type"] : null;

if (!$pagamento_id) {
    header("Location: caixa.php");
    exit();
}

// Se pagamento aprovado, atualiza o banco
if ($status === "sucesso" && $mp_status === "approved") {
    $hoje = date("Y-m-d");
    $mp_id_safe = $conexao->real_escape_string($mp_payment_id);
    $metodo_safe = $conexao->real_escape_string($metodo);

    $sql_update = "UPDATE pagamentos SET 
                        status = 'pago',
                        data_pagamento = '$hoje',
                        mp_payment_id = '$mp_id_safe',
                        metodo_pagamento = '$metodo_safe'
                   WHERE id = $pagamento_id AND aluno_id = " . (int)$_SESSION["id"];

    $conexao->query($sql_update);

    header("Location: caixa.php?sucesso=1");
    exit();

} elseif ($status === "pendente") {
    header("Location: caixa.php?pendente=1");
    exit();

} else {
    header("Location: caixa.php?falha=1");
    exit();
}
?>