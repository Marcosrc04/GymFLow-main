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

// Totais do caixa
$mes_atual = date("Y-m");

$sql_total_recebido = "SELECT COALESCE(SUM(valor), 0) as total FROM pagamentos WHERE status = 'pago' AND DATE_FORMAT(data_pagamento, '%Y-%m') = '$mes_atual'";
$total_recebido = $conexao->query($sql_total_recebido)->fetch_assoc()["total"];

$sql_total_pendente = "SELECT COALESCE(SUM(valor), 0) as total FROM pagamentos WHERE status = 'pendente'";
$total_pendente = $conexao->query($sql_total_pendente)->fetch_assoc()["total"];

$sql_total_atrasado = "SELECT COALESCE(SUM(valor), 0) as total FROM pagamentos WHERE status = 'atrasado' OR (status != 'pago' AND vencimento < CURDATE())";
$total_atrasado = $conexao->query($sql_total_atrasado)->fetch_assoc()["total"];

$sql_total_mes = "SELECT COALESCE(SUM(valor), 0) as total FROM pagamentos WHERE DATE_FORMAT(vencimento, '%Y-%m') = '$mes_atual'";
$total_mes = $conexao->query($sql_total_mes)->fetch_assoc()["total"];

// Lista de pagamentos
$sql_pagamentos = "SELECT 
                        p.id,
                        u.nome,
                        u.email,
                        p.valor,
                        p.vencimento,
                        p.data_pagamento,
                        p.status,
                        p.mp_payment_id,
                        p.metodo_pagamento
                   FROM pagamentos p
                   INNER JOIN usuarios u ON p.aluno_id = u.id
                   WHERE u.tipo = 'aluno'
                   ORDER BY p.vencimento DESC";

$resultado_pagamentos = $conexao->query($sql_pagamentos);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa | GymFlow Admin</title>
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
            <ul class="nav-links">
                <li><a href="dashboard-admin.php">Dashboard</a></li>
                <li><a href="alunos.php">Alunos</a></li>
                <li><a href="pagamentos.php">Mensalidades</a></li>
                <li><a href="caixa.php" class="ativo">Caixa</a></li>
                <li><a href="#">Treinos</a></li>
                <li><a href="../../logout.php">Sair</a></li>
            </ul>
        </nav>
    </aside>

    <main class="conteudo-dashboard">
        <header class="topo-dashboard">
            <h1>Caixa</h1>
            <p>Resumo financeiro da academia — <?php echo date("m/Y"); ?></p>
        </header>

        <!-- Cards resumo financeiro -->
        <section class="cards-resumo">
            <div class="card-resumo">
                <h3>Recebido no mês</h3>
                <p>R$ <?php echo number_format((float)$total_recebido, 2, ',', '.'); ?></p>
            </div>
            <div class="card-resumo">
                <h3>Pendente</h3>
                <p>R$ <?php echo number_format((float)$total_pendente, 2, ',', '.'); ?></p>
            </div>
            <div class="card-resumo">
                <h3>Atrasado</h3>
                <p>R$ <?php echo number_format((float)$total_atrasado, 2, ',', '.'); ?></p>
            </div>
            <div class="card-resumo">
                <h3>Total previsto no mês</h3>
                <p>R$ <?php echo number_format((float)$total_mes, 2, ',', '.'); ?></p>
            </div>
        </section>

        <section class="blocos-dashboard-admin">
            <div class="bloco-dashboard-1">
                <h2>Todos os Pagamentos</h2>

                <div class="tabela-scroll">
                    <table class="tabela-dashboard">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Pagamento</th>
                                <th>Método</th>
                                <th>Status</th>
                                <th>ID MercadoPago</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado_pagamentos && $resultado_pagamentos->num_rows > 0): ?>
                                <?php while ($p = $resultado_pagamentos->fetch_assoc()): ?>
                                    <?php
                                    $status = $p["status"];
                                    if ($status != "pago" && $p["vencimento"] < date("Y-m-d")) {
                                        $status = "atrasado";
                                    }
                                    $dataPag = (!empty($p["data_pagamento"]) && $p["data_pagamento"] != "0000-00-00")
                                        ? date("d/m/Y", strtotime($p["data_pagamento"])) : "-";
                                    $metodo = !empty($p["metodo_pagamento"]) ? ucfirst($p["metodo_pagamento"]) : "-";
                                    $mp_id = !empty($p["mp_payment_id"]) ? $p["mp_payment_id"] : "-";
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p["nome"]); ?></td>
                                        <td>R$ <?php echo number_format((float)$p["valor"], 2, ',', '.'); ?></td>
                                        <td><?php echo date("d/m/Y", strtotime($p["vencimento"])); ?></td>
                                        <td><?php echo $dataPag; ?></td>
                                        <td><?php echo htmlspecialchars($metodo); ?></td>
                                        <td>
                                            <span class="status <?php echo htmlspecialchars($status); ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td style="font-size:0.75rem; color:#888;"><?php echo htmlspecialchars($mp_id); ?></td>
                                        <td class="coluna-acoes">
                                            <?php if ($p["status"] != "pago"): ?>
                                                <a href="marcar-pago.php?id=<?php echo (int)$p["id"]; ?>">Marcar pago</a>
                                            <?php else: ?>
                                                ✔ Pago
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8">Nenhum pagamento encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

    <script src="../../arquivos/js/script.js"></script>
</body>
</html>