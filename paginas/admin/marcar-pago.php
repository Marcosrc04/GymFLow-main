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

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    header("Location: pagamentos.php");
    exit();
}

$sql = "UPDATE pagamentos 
        SET status = 'pago',
            data_pagamento = CURDATE()
        WHERE id = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: pagamentos.php");
    exit();
} else {
    die("Erro ao marcar pagamento: " . $conexao->error);
}