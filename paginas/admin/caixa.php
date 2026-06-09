<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {
    header("Location: ../../login.php");
    exit();
}

include("../../includes/conexao.php");

date_default_timezone_set("America/Sao_Paulo");

$mes_atual = date("Y-m");

$total_recebido = $conexao->query("SELECT COALESCE(SUM(valor), 0) as total FROM pagamentos WHERE status = 'pago' AND DATE_FORMAT(data_pagamento, '%Y-%m') = '$mes_atual'")->fetch_assoc()["total"];
$total_pendente = $conexao->query("SELECT COALESCE(SUM(valor), 0) as total FROM pagamentos WHERE status = 'pendente'")->fetch_assoc()["total"];
$total_atrasado = $conexao->query("SELECT COALESCE(SUM(valor), 0) as total FROM pagamentos WHERE status = 'atrasado' OR (status != 'pago' AND vencimento < CURDATE())")->fetch_assoc()["total"];
$total_mes      = $conexao->query("SELECT COALESCE(SUM(valor), 0) as total FROM pagamentos WHERE DATE_FORMAT(vencimento, '%Y-%m') = '$mes_atual'")->fetch_assoc()["total"];

$resultado_pagamentos = $conexao->query("SELECT p.id, u.nome, u.email, p.valor, p.vencimento, p.data_pagamento, p.status, p.mp_payment_id, p.metodo_pagamento
    FROM pagamentos p
    INNER JOIN usuarios u ON p.aluno_id = u.id
    WHERE u.tipo = 'aluno'
    ORDER BY p.vencimento DESC");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa | GymFlow Admin</title>
    <link rel="shortcut icon" href="../../arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../arquivos/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="topbar">
    <div class="topbar-esquerda">
        <button class="hamburger-btn" id="hamburger-btn"><span></span><span></span><span></span></button>
        <div class="topbar-logo-box">🏋️</div>
        <div class="topbar-titulo"><h1>GymFlow</h1><p>Área Administrativa</p></div>
    </div>
    <div class="topbar-direita">
        <div class="topbar-usuario">
            <div class="nome"><?php echo htmlspecialchars($_SESSION["nome"]); ?></div>
            <div class="cargo">Administrador</div>
        </div>
        <a href="/GymFlow-main/logout.php" class="btn-sair">Sair</a>
    </div>
</div>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

<div class="layout">
    <aside class="sidebar" id="sidebar">
        <span class="sidebar-label">Menu</span>
        <a href="/GymFlow-main/paginas/admin/dashboard-admin.php"><span class="icon">📊</span> Dashboard</a>
        <a href="/GymFlow-main/paginas/aluno/gerar_treino_com_ia/gerar-treino.php"><span class="icon">🏋️</span> Meus Treinos</a>
        <a href="/GymFlow-main/paginas/admin/alunos.php"><span class="icon">👥</span> Alunos</a>
        <a href="/GymFlow-main/paginas/admin/pagamentos.php"><span class="icon">💳</span> Mensalidades</a>
        <a href="/GymFlow-main/paginas/admin/caixa.php" class="ativo"><span class="icon">💰</span> Caixa</a>
        <a href="#"><span class="icon">🏋️</span> Treinos</a>
        <span class="sidebar-label">Conta</span>
        <a href="/GymFlow-main/paginas/aluno/perfil.php"><span class="icon">👤</span> Meu Perfil</a>
        <a href="/GymFlow-main/logout.php"><span class="icon">🚪</span> Sair</a>
    </aside>

    <main class="main">
        <div class="page-header">
            <div class="saudacao">Caixa <span>Financeiro</span></div>
            <p class="sub">Resumo financeiro da academia — <?php echo date("m/Y"); ?></p>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card verde">
                <div class="kpi-icon">💵</div>
                <div class="kpi-label">Recebido no mês</div>
                <div class="kpi-valor">R$<?php echo number_format((float)$total_recebido, 0, ',', '.'); ?></div>
                <div class="kpi-sub"><?php echo date("m/Y"); ?></div>
            </div>
            <div class="kpi-card amarelo">
                <div class="kpi-icon">⏳</div>
                <div class="kpi-label">Pendente</div>
                <div class="kpi-valor">R$<?php echo number_format((float)$total_pendente, 0, ',', '.'); ?></div>
                <div class="kpi-sub">aguardando</div>
            </div>
            <div class="kpi-card vermelho">
                <div class="kpi-icon">⚠️</div>
                <div class="kpi-label">Atrasado</div>
                <div class="kpi-valor">R$<?php echo number_format((float)$total_atrasado, 0, ',', '.'); ?></div>
                <div class="kpi-sub">em atraso</div>
            </div>
            <div class="kpi-card azul">
                <div class="kpi-icon">📊</div>
                <div class="kpi-label">Total previsto no mês</div>
                <div class="kpi-valor">R$<?php echo number_format((float)$total_mes, 0, ',', '.'); ?></div>
                <div class="kpi-sub"><?php echo date("m/Y"); ?></div>
            </div>
        </div>

        <div class="secao-header">
            <div class="secao-header-esquerda">
                <h2>Todos os Pagamentos</h2>
            </div>
        </div>

        <div class="tabela-card">
            <div class="tabela-scroll">
                <table>
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
                                $metodo  = !empty($p["metodo_pagamento"]) ? ucfirst($p["metodo_pagamento"]) : "-";
                                $mp_id   = !empty($p["mp_payment_id"]) ? $p["mp_payment_id"] : "-";
                                ?>
                                <tr>
                                    <td class="td-nome"><?php echo htmlspecialchars($p["nome"]); ?></td>
                                    <td>R$ <?php echo number_format((float)$p["valor"], 2, ',', '.'); ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($p["vencimento"])); ?></td>
                                    <td><?php echo $dataPag; ?></td>
                                    <td><?php echo htmlspecialchars($metodo); ?></td>
                                    <td><span class="status <?php echo htmlspecialchars($status); ?>"><?php echo ucfirst($status); ?></span></td>
                                    <td class="td-mp-id"><?php echo htmlspecialchars($mp_id); ?></td>
                                    <td class="coluna-acoes">
                                        <?php if ($p["status"] != "pago"): ?>
                                            <a href="/GymFlow-main/paginas/admin/marcar-pago.php?id=<?php echo (int)$p["id"]; ?>">Marcar pago</a>
                                        <?php else: ?>
                                            ✔ Pago
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="td-vazio">Nenhum pagamento encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    hamburgerBtn.addEventListener('click', () => {
        hamburgerBtn.classList.toggle('aberto');
        sidebar.classList.toggle('aberta');
        overlay.classList.toggle('ativo');
    });
    overlay.addEventListener('click', () => {
        hamburgerBtn.classList.remove('aberto');
        sidebar.classList.remove('aberta');
        overlay.classList.remove('ativo');
    });
</script>

</body>
</html>