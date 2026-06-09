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

$hora = date("H");
if ($hora < 12) $saudacao = "Bom dia";
elseif ($hora < 18) $saudacao = "Boa tarde";
else $saudacao = "Boa noite";

$total_alunos    = $conexao->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'aluno'")->fetch_assoc()["total"] ?? 0;
$total_pagas     = $conexao->query("SELECT COUNT(*) as total FROM pagamentos WHERE status = 'pago'")->fetch_assoc()["total"] ?? 0;
$total_atrasados = $conexao->query("SELECT COUNT(*) as total FROM pagamentos WHERE status != 'pago' AND vencimento < CURDATE()")->fetch_assoc()["total"] ?? 0;
$total_pendentes = $conexao->query("SELECT COUNT(*) as total FROM pagamentos WHERE status = 'pendente' AND vencimento >= CURDATE()")->fetch_assoc()["total"] ?? 0;
$receita_mes     = $conexao->query("SELECT COALESCE(SUM(valor),0) as total FROM pagamentos WHERE status = 'pago' AND DATE_FORMAT(data_pagamento,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')")->fetch_assoc()["total"] ?? 0;

$resultado_alunos = $conexao->query("SELECT u.id, u.nome, u.email, u.foto, p.status, p.vencimento
    FROM usuarios u
    LEFT JOIN pagamentos p ON p.id = (SELECT MAX(id) FROM pagamentos WHERE aluno_id = u.id)
    WHERE u.tipo = 'aluno'
    ORDER BY u.nome ASC");

$foto_admin    = $_SESSION["foto"] ?? null;
$inicial_admin = strtoupper(substr($_SESSION["nome"], 0, 1));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GymFlow Admin</title>
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
        <div class="topbar-avatar">
            <?php if (!empty($foto_admin)): ?>
                <img src="../../arquivos/imagem/perfis/<?php echo htmlspecialchars($foto_admin); ?>" alt="">
            <?php else: ?>
                <?php echo $inicial_admin; ?>
            <?php endif; ?>
        </div>
        <div class="topbar-usuario">
            <div class="nome"><?php echo htmlspecialchars($_SESSION["nome"]); ?></div>
            <div class="cargo">Administrador</div>
        </div>
        <a href="../../logout.php" class="btn-sair">Sair</a>
    </div>
</div>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

<div class="layout">
    <aside class="sidebar" id="sidebar">
        <span class="sidebar-label">Menu</span>
        <a href="/GymFlow-main/paginas/admin/dashboard-admin.php" class="ativo"><span class="icon">📊</span> Dashboard</a>
        <a href="/GymFlow-main/paginas/aluno/gerar_treino_com_ia/gerar-treino.php"><span class="icon">🏋️</span> Meus Treinos</a>
        <a href="/GymFlow-main/paginas/admin/alunos.php"><span class="icon">👥</span> Alunos</a>
        <a href="/GymFlow-main/paginas/admin/pagamentos.php"><span class="icon">💳</span> Mensalidades</a>
        <a href="/GymFlow-main/paginas/admin/caixa.php"><span class="icon">💰</span> Caixa</a>
        <a href="#"><span class="icon">🏋️</span> Treinos</a>
        <span class="sidebar-label">Conta</span>
        <a href="/GymFlow-main/paginas/aluno/perfil.php"><span class="icon">👤</span> Meu Perfil</a>
        <a href="/GymFlow-main/logout.php"><span class="icon">🚪</span> Sair</a>
    </aside>

    <main class="main">
        <div class="page-header">
            <div class="saudacao"><?php echo $saudacao; ?>, <span><?php echo htmlspecialchars(explode(' ', $_SESSION["nome"])[0]); ?></span> 👋</div>
            <p class="sub">Aqui está o resumo da sua academia hoje, <?php echo date("d/m/Y"); ?></p>
        </div>

        <div class="kpi-grid kpi-grid-5">
            <div class="kpi-card">
                <div class="kpi-icon">👥</div>
                <div class="kpi-label">Total de Alunos</div>
                <div class="kpi-valor"><?php echo $total_alunos; ?></div>
                <div class="kpi-sub">cadastrados no sistema</div>
            </div>
            <div class="kpi-card verde">
                <div class="kpi-icon">✅</div>
                <div class="kpi-label">Mensalidades Pagas</div>
                <div class="kpi-valor"><?php echo $total_pagas; ?></div>
                <div class="kpi-sub">no total</div>
            </div>
            <div class="kpi-card vermelho">
                <div class="kpi-icon">⚠️</div>
                <div class="kpi-label">Atrasados</div>
                <div class="kpi-valor"><?php echo $total_atrasados; ?></div>
                <div class="kpi-sub">precisam de atenção</div>
            </div>
            <div class="kpi-card amarelo">
                <div class="kpi-icon">⏳</div>
                <div class="kpi-label">Pendentes</div>
                <div class="kpi-valor"><?php echo $total_pendentes; ?></div>
                <div class="kpi-sub">aguardando pagamento</div>
            </div>
            <div class="kpi-card azul">
                <div class="kpi-icon">💰</div>
                <div class="kpi-label">Receita do Mês</div>
                <div class="kpi-valor">R$<?php echo number_format((float)$receita_mes, 0, ',', '.'); ?></div>
                <div class="kpi-sub"><?php echo date("m/Y"); ?></div>
            </div>
        </div>

        <div class="secao-header">
            <div class="secao-header-esquerda">
                <h2>Alunos Cadastrados</h2>
                <span class="badge-total"><?php echo $total_alunos; ?> alunos</span>
            </div>
            <a href="/GymFlow-main/cadastro.php" class="btn-novo-aluno">+ Novo aluno</a>
        </div>

        <div class="tabela-card">
            <div class="tabela-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th class="coluna-email">E-mail</th>
                            <th>Status</th>
                            <th>Vencimento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_alunos && $resultado_alunos->num_rows > 0): ?>
                            <?php while ($aluno = $resultado_alunos->fetch_assoc()): ?>
                                <?php
                                $status = $aluno["status"] ?? "sem-status";
                                if ($status != "pago" && !empty($aluno["vencimento"]) && $aluno["vencimento"] < date("Y-m-d")) {
                                    $status = "atrasado";
                                }
                                $statusTexto = $status == "sem-status" ? "Sem registro" : ucfirst($status);
                                $vencimento  = !empty($aluno["vencimento"]) ? date("d/m/Y", strtotime($aluno["vencimento"])) : "-";
                                $inicial     = strtoupper(substr($aluno["nome"], 0, 1));
                                ?>
                                <tr>
                                    <td class="td-aluno-nome">
                                        <span class="td-avatar">
                                            <?php if (!empty($aluno["foto"])): ?>
                                                <img src="../../arquivos/imagem/perfis/<?php echo htmlspecialchars($aluno["foto"]); ?>" alt="">
                                            <?php else: ?>
                                                <?php echo $inicial; ?>
                                            <?php endif; ?>
                                        </span>
                                        <span class="td-nome"><?php echo htmlspecialchars($aluno["nome"]); ?></span>
                                    </td>
                                    <td class="coluna-email"><?php echo htmlspecialchars($aluno["email"]); ?></td>
                                    <td><span class="status-badge <?php echo $status; ?>"><?php echo $statusTexto; ?></span></td>
                                    <td><?php echo $vencimento; ?></td>
                                    <td class="coluna-acoes">
                                        <a href="/GymFlow-main/paginas/admin/editar-aluno.php?id=<?php echo (int)$aluno["id"]; ?>">Editar</a>
                                        <span>|</span>
                                        <a href="/GymFlow-main/paginas/aluno/perfil.php?id=<?php echo (int)$aluno["id"]; ?>">Ver</a>
                                        <span>|</span>
                                        <a href="/GymFlow-main/paginas/admin/excluir-aluno.php?id=<?php echo (int)$aluno["id"]; ?>" onclick="return confirm('Excluir este aluno?')" class="link-excluir">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="td-vazio">Nenhum aluno cadastrado.</td></tr>
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