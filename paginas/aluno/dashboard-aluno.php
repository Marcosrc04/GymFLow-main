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

$hora = date("H");
if ($hora < 12) $saudacao = "Bom dia";
elseif ($hora < 18) $saudacao = "Boa tarde";
else $saudacao = "Boa noite";

$aluno_id = (int)$_SESSION["id"];

$pagamento = $conexao->query("SELECT status, vencimento, valor FROM pagamentos WHERE aluno_id = $aluno_id ORDER BY id DESC LIMIT 1")->fetch_assoc();

$status_mensalidade = "Sem registro";
$vencimento_fmt     = "-";
$status_classe      = "sem-status";

if ($pagamento) {
    $status_classe = $pagamento["status"];
    if ($status_classe != "pago" && $pagamento["vencimento"] < date("Y-m-d")) {
        $status_classe = "atrasado";
    }
    $status_mensalidade = ucfirst($status_classe);
    $vencimento_fmt     = date("d/m/Y", strtotime($pagamento["vencimento"]));
}

$total_pagas = $conexao->query("SELECT COUNT(*) as total FROM pagamentos WHERE aluno_id = $aluno_id AND status = 'pago'")->fetch_assoc()["total"] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GymFlow</title>
    <link rel="shortcut icon" href="../../arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../arquivos/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="topbar">
    <div class="topbar-esquerda">
        <button class="hamburger-btn" id="hamburger-btn"><span></span><span></span><span></span></button>
        <div class="topbar-logo-box">🏋️</div>
        <div class="topbar-titulo"><h1>GymFlow</h1><p>Área do Aluno</p></div>
    </div>
    <div class="topbar-direita">
        <div class="topbar-avatar">
            <?php if (!empty($_SESSION["foto"])): ?>
                <img src="../../arquivos/imagem/perfis/<?php echo htmlspecialchars($_SESSION["foto"]); ?>" alt="">
            <?php else: ?>
                <?php echo strtoupper(substr($_SESSION["nome"], 0, 1)); ?>
            <?php endif; ?>
        </div>
        <div class="topbar-usuario">
            <div class="nome"><?php echo htmlspecialchars(explode(' ', $_SESSION["nome"])[0]); ?></div>
            <div class="cargo">Aluno</div>
        </div>
        <a href="/GymFlow-main/logout.php" class="btn-sair">Sair</a>
    </div>
</div>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

<div class="layout">
    <aside class="sidebar" id="sidebar">
        <span class="sidebar-label">Menu</span>
        <a href="/GymFlow-main/paginas/aluno/dashboard-aluno.php" class="ativo"><span class="icon">📊</span> Dashboard</a>
        <a href="/GymFlow-main/paginas/aluno/gerar_treino_com_ia/gerar-treino.php"><span class="icon">🏋️</span> Meus Treinos</a>
        <a href="/GymFlow-main/paginas/aluno/caixa.php"><span class="icon">💳</span> Mensalidades</a>
        <span class="sidebar-label">Conta</span>
        <a href="/GymFlow-main/paginas/aluno/perfil.php"><span class="icon">👤</span> Meu Perfil</a>
        <a href="/GymFlow-main/logout.php"><span class="icon">🚪</span> Sair</a>
    </aside>

    <main class="main">
        <div class="page-header">
            <div class="saudacao"><?php echo $saudacao; ?>, <span><?php echo htmlspecialchars(explode(' ', $_SESSION["nome"])[0]); ?></span> 👋</div>
            <p class="sub">Acompanhe seu desenvolvimento e fique sempre atualizado 💪</p>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon">🏋️</div>
                <div class="kpi-label">Plano Atual</div>
                <div class="kpi-valor" style="font-size:20px">Premium</div>
                <div class="kpi-sub">plano ativo</div>
            </div>
            <div class="kpi-card <?php echo $status_classe == 'pago' ? 'verde' : ($status_classe == 'atrasado' ? 'vermelho' : 'amarelo'); ?>">
                <div class="kpi-icon"><?php echo $status_classe == 'pago' ? '✅' : ($status_classe == 'atrasado' ? '⚠️' : '⏳'); ?></div>
                <div class="kpi-label">Mensalidade</div>
                <div class="kpi-valor" style="font-size:20px"><?php echo $status_mensalidade; ?></div>
                <div class="kpi-sub">vence <?php echo $vencimento_fmt; ?></div>
            </div>
            <div class="kpi-card verde">
                <div class="kpi-icon">✅</div>
                <div class="kpi-label">Mensalidades Pagas</div>
                <div class="kpi-valor"><?php echo $total_pagas; ?></div>
                <div class="kpi-sub">no total</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon">📊</div>
                <div class="kpi-label">IMC</div>
                <div class="kpi-valor">23.4</div>
                <div class="kpi-sub">peso normal</div>
            </div>
        </div>

        <div class="blocos-grid">
            <div class="bloco">
                <div class="bloco-titulo"><span>🔥</span> Treino de Hoje</div>
                <div class="exercicio-item">
                    <div class="exercicio-num">1</div>
                    <div class="exercicio-info"><div class="nome">Supino Reto</div><div class="series">4 séries × 10 repetições</div></div>
                </div>
                <div class="exercicio-item">
                    <div class="exercicio-num">2</div>
                    <div class="exercicio-info"><div class="nome">Supino Inclinado</div><div class="series">3 séries × 12 repetições</div></div>
                </div>
                <div class="exercicio-item">
                    <div class="exercicio-num">3</div>
                    <div class="exercicio-info"><div class="nome">Crucifixo</div><div class="series">3 séries × 12 repetições</div></div>
                </div>
                <div class="exercicio-item">
                    <div class="exercicio-num">4</div>
                    <div class="exercicio-info"><div class="nome">Tríceps Corda</div><div class="series">3 séries × 15 repetições</div></div>
                </div>
                <a href="/GymFlow-main/paginas/aluno/gerar_treino_com_ia/gerar-treino.php" class="btn-gerar-ia">🤖 Gerar novo treino com IA</a>
            </div>

            <div class="bloco">
                <div class="bloco-titulo"><span>💳</span> Status da Mensalidade</div>
                <div class="mensalidade-status">
                    <div class="status-icon <?php echo $status_classe; ?>">
                        <?php echo $status_classe == 'pago' ? '✅' : ($status_classe == 'atrasado' ? '⚠️' : '⏳'); ?>
                    </div>
                    <div class="status-info">
                        <div class="label">Situação atual</div>
                        <div class="valor">
                            <span class="status-badge-grande <?php echo $status_classe; ?>"><?php echo $status_mensalidade; ?></span>
                        </div>
                    </div>
                </div>
                <div class="mensalidade-detalhe">
                    <div class="item"><div class="dl">Vencimento</div><div class="dv"><?php echo $vencimento_fmt; ?></div></div>
                    <div class="item"><div class="dl">Valor</div><div class="dv"><?php echo $pagamento ? 'R$ ' . number_format((float)$pagamento["valor"], 2, ',', '.') : '-'; ?></div></div>
                    <div class="item"><div class="dl">Pagas</div><div class="dv"><?php echo $total_pagas; ?></div></div>
                </div>
                <?php if ($status_classe != 'pago'): ?>
                    <a href="/GymFlow-main/paginas/aluno/caixa.php" class="btn-pagar-agora">💳 Pagar agora</a>
                <?php else: ?>
                    <div class="mensalidade-ok">✅ Mensalidade em dia!</div>
                <?php endif; ?>
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