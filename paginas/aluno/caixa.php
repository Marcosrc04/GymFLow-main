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

date_default_timezone_set("America/Sao_Paulo");

$aluno_id = (int)$_SESSION["id"];

$sql = "SELECT * FROM pagamentos WHERE aluno_id = $aluno_id ORDER BY vencimento DESC";
$resultado = $conexao->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa | GymFlow</title>
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
                <li><a href="dashboard-aluno.php">Dashboard</a></li>
                <li><a href="caixa.php" class="ativo">Mensalidades</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="../../logout.php">Sair</a></li>
            </ul>
        </nav>
    </aside>

    <main class="conteudo-dashboard">
        <header class="topo-dashboard">
            <h1>Minhas Mensalidades</h1>
            <p>Acompanhe e pague suas mensalidades</p>
        </header>

        <?php if (isset($_GET["sucesso"])): ?>
            <div class="alerta alerta-sucesso">✅ Pagamento realizado com sucesso! Sua mensalidade foi atualizada.</div>
        <?php endif; ?>

        <?php if (isset($_GET["pendente"])): ?>
            <div class="alerta alerta-aviso">⏳ Pagamento pendente. Aguardando confirmação.</div>
        <?php endif; ?>

        <?php if (isset($_GET["falha"])): ?>
            <div class="alerta alerta-erro">❌ Pagamento não concluído. Tente novamente.</div>
        <?php endif; ?>

        <section class="blocos-dashboard-admin">
            <div class="bloco-dashboard-1">
                <div class="tabela-scroll">
                    <table class="tabela-dashboard">
                        <thead>
                            <tr>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Pagamento</th>
                                <th>Método</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado && $resultado->num_rows > 0): ?>
                                <?php while ($p = $resultado->fetch_assoc()): ?>
                                    <?php
                                    $status = $p["status"];
                                    if ($status != "pago" && $p["vencimento"] < date("Y-m-d")) {
                                        $status = "atrasado";
                                    }
                                    $dataPag = (!empty($p["data_pagamento"]) && $p["data_pagamento"] != "0000-00-00")
                                        ? date("d/m/Y", strtotime($p["data_pagamento"])) : "-";
                                    $metodo = !empty($p["metodo_pagamento"]) ? ucfirst($p["metodo_pagamento"]) : "-";
                                    ?>
                                    <tr>
                                        <td><?php echo date("d/m/Y", strtotime($p["vencimento"])); ?></td>
                                        <td>R$ <?php echo number_format((float)$p["valor"], 2, ',', '.'); ?></td>
                                        <td>
                                            <span class="status <?php echo htmlspecialchars($status); ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $dataPag; ?></td>
                                        <td><?php echo htmlspecialchars($metodo); ?></td>
                                        <td class="coluna-acoes">
                                            <?php if ($status != "pago"): ?>
                                                <a href="processar-pagamento.php?id=<?php echo (int)$p["id"]; ?>" class="btn-pagar">
                                                    💳 Pagar agora
                                                </a>
                                            <?php else: ?>
                                                ✔ Pago
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">Nenhuma mensalidade encontrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

</body>
</html>