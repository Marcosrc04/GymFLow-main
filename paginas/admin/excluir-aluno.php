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

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    $sql_pagamentos = "DELETE FROM pagamentos WHERE aluno_id = $id";
    if (!$conexao->query($sql_pagamentos)) {
        die("Erro ao excluir pagamentos: " . $conexao->error);
    }

    $sql_usuario = "DELETE FROM usuarios WHERE id = $id AND tipo = 'aluno'";
    if (!$conexao->query($sql_usuario)) {
        die("Erro ao excluir aluno: " . $conexao->error);
    }
}

header("Location: dashboard-admin.php");
exit();
?>