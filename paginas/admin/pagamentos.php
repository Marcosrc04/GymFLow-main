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

date_default_timezone_set("America/Sao_Paulo");

$sql_pagamentos = "SELECT 
                        p.id,
                        u.nome,
                        p.valor,
                        p.vencimento,
                        p.data_pagamento,
                        p.status
                   FROM pagamentos p
                   INNER JOIN usuarios u ON p.aluno_id = u.id
                   WHERE u.tipo = 'aluno'
                   ORDER BY p.vencimento ASC";

$resultado_pagamentos = $conexao->query($sql_pagamentos);

if (!$resultado_pagamentos) {
    die("Erro na consulta: " . $conexao->error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensalidades | GymFlow</title>
    <link rel="shortcut icon" href="../../arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../arquivos/css/style.css">
</head>
<body>

<div class="layout-dashboard">

    <aside class="sidebar">
        <div class="topo-sidebar">
            <img src="../../arquivos/imagem/1312fedc-983d-42d0-80df-ada4981193f2.png" alt="Logo GymFlow" class="logo-sidebar">
            <h2>GymFlow</h2>
        </div>

        <nav class="menu-sidebar">
            <a href="dashboard-admin.php">Dashboard</a>
            <a href="alunos.php">Alunos</a>
            <a href="pagamentos.php">Mensalidades</a>
            <a href="#">Treinos</a>
            <a href="../../logout.php">Sair</a>
        </nav>
    </aside>

    <main class="conteudo-dashboard">
        <header class="topo-dashboard">
            <h1>Mensalidades</h1>
            <p>Gerencie os pagamentos dos alunos</p>
        </header>

        <section class="blocos-dashboard-admin">
            <div class="bloco-dashboard-1">
                <a href="nova-mensalidade.php" class="btn-novo">+ Nova mensalidade</a>

                <div class="tabela-scroll">
                    <table class="tabela-dashboard">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Pagamento</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($resultado_pagamentos->num_rows > 0) { ?>
                                <?php while ($pagamento = $resultado_pagamentos->fetch_assoc()) { ?>
                                    <?php
                                    $status = $pagamento["status"];

                                    if ($status != "pago" && $pagamento["vencimento"] < date("Y-m-d")) {
                                        $status = "atrasado";
                                    }

                                    $dataPagamento = "-";
                                    if (!empty($pagamento["data_pagamento"]) && $pagamento["data_pagamento"] != "0000-00-00") {
                                        $dataPagamento = date("d/m/Y", strtotime($pagamento["data_pagamento"]));
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pagamento["nome"]); ?></td>
                                        <td>R$ <?php echo number_format((float)$pagamento["valor"], 2, ',', '.'); ?></td>
                                        <td><?php echo date("d/m/Y", strtotime($pagamento["vencimento"])); ?></td>
                                        <td><?php echo $dataPagamento; ?></td>
                                        <td>
                                            <span class="status <?php echo htmlspecialchars($status); ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td class="coluna-acoes">
                                            <?php if ($pagamento["status"] != "pago") { ?>
                                                <a href="marcar-pago.php?id=<?php echo (int)$pagamento["id"]; ?>">Marcar como pago</a>
                                            <?php } else { ?>
                                                ✔ Pago
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="6">Nenhuma mensalidade encontrada.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

</body>
</html>