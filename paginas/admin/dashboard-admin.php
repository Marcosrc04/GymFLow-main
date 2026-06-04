<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$hora = date("H");
if ($hora < 12) $saudacao = "Bom dia";
elseif ($hora < 18) $saudacao = "Boa tarde";
else $saudacao = "Boa noite";

$sql_total_alunos = "SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'aluno'";
$total_alunos = $conexao->query($sql_total_alunos)->fetch_assoc()["total"] ?? 0;

$sql_pagas = "SELECT COUNT(*) as total FROM pagamentos WHERE status = 'pago'";
$total_pagas = $conexao->query($sql_pagas)->fetch_assoc()["total"] ?? 0;

$sql_atrasados = "SELECT COUNT(*) as total FROM pagamentos WHERE status != 'pago' AND vencimento < CURDATE()";
$total_atrasados = $conexao->query($sql_atrasados)->fetch_assoc()["total"] ?? 0;

$sql_pendentes = "SELECT COUNT(*) as total FROM pagamentos WHERE status = 'pendente' AND vencimento >= CURDATE()";
$total_pendentes = $conexao->query($sql_pendentes)->fetch_assoc()["total"] ?? 0;

$sql_receita = "SELECT COALESCE(SUM(valor),0) as total FROM pagamentos WHERE status = 'pago' AND DATE_FORMAT(data_pagamento,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')";
$receita_mes = $conexao->query($sql_receita)->fetch_assoc()["total"] ?? 0;

$sql_alunos = "SELECT u.id, u.nome, u.email, p.status, p.vencimento
               FROM usuarios u
               LEFT JOIN pagamentos p ON p.id = (SELECT MAX(id) FROM pagamentos WHERE aluno_id = u.id)
               WHERE u.tipo = 'aluno'
               ORDER BY u.nome ASC";
$resultado_alunos = $conexao->query($sql_alunos);
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
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }

        /* ── TOPBAR ── */
        .topbar {
            width: 100%;
            background: #111;
            border-bottom: 1px solid rgba(255, 204, 0, 0.1);
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-esquerda {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-logo-box {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #f5c518, #ffd84d);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .topbar-titulo h1 {
            color: #fff;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.2;
        }

        .topbar-titulo p {
            color: #888;
            font-size: 11px;
        }

        .topbar-direita {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-usuario {
            text-align: right;
        }

        .topbar-usuario .nome {
            color: #f5c518;
            font-size: 14px;
            font-weight: 600;
        }

        .topbar-usuario .cargo {
            color: #888;
            font-size: 11px;
        }

        .btn-sair {
            border: 1px solid rgba(245, 197, 24, 0.4);
            color: #f5c518;
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s;
        }

        .btn-sair:hover {
            background: #f5c518;
            color: #111;
        }

        /* ── HAMBURGER ── */
        .hamburger-btn {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 6px;
            background: #1e1e1e;
            border-radius: 8px;
            border: 1px solid rgba(255, 204, 0, 0.15);
        }

        .hamburger-btn span {
            display: block;
            width: 20px;
            height: 2px;
            background: #f5c518;
            border-radius: 2px;
            transition: 0.3s;
        }

        .hamburger-btn.aberto span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .hamburger-btn.aberto span:nth-child(2) {
            opacity: 0;
        }

        .hamburger-btn.aberto span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* ── LAYOUT ── */
        .layout {
            display: flex;
            min-height: calc(100vh - 64px);
            background: #0a0a0a;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 240px;
            background: #111;
            border-right: 1px solid rgba(255, 204, 0, 0.08);
            padding: 28px 16px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            background-image: url(../../arquivos/imagem/listras-da-barra.png);
            background-size: cover;
            background-position: center;
        }

        .sidebar-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #555;
            padding: 0 12px;
            margin: 16px 0 8px;
        }

        .sidebar-label:first-child {
            margin-top: 0;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            border-radius: 10px;
            color: #aaa;
            font-size: 14px;
            font-weight: 500;
            transition: 0.2s;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: rgba(245, 197, 24, 0.08);
            color: #f5c518;
        }

        .sidebar a.ativo {
            background: rgba(245, 197, 24, 0.12);
            color: #f5c518;
            border: 1px solid rgba(245, 197, 24, 0.2);
        }

        .sidebar a .icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        /* ── MAIN ── */
        .main {
            flex: 1;
            padding: 36px;
            min-width: 0;
            overflow-x: hidden;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            margin-bottom: 32px;
        }

        .page-header .saudacao {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 42px;
            line-height: 1;
            color: #fff;
            letter-spacing: 1px;
        }

        .page-header .saudacao span {
            color: #f5c518;
        }

        .page-header .sub {
            color: #666;
            font-size: 14px;
            margin-top: 6px;
        }

        /* ── KPI CARDS ── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .kpi-card {
            background: #161616;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: 0.2s;
        }

        .kpi-card:hover {
            border-color: rgba(245, 197, 24, 0.2);
            transform: translateY(-2px);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--cor, #f5c518);
            border-radius: 16px 16px 0 0;
        }

        .kpi-card .kpi-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(245, 197, 24, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 14px;
        }

        .kpi-card .kpi-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kpi-card .kpi-valor {
            font-size: 32px;
            font-family: 'Bebas Neue', sans-serif;
            color: #fff;
            letter-spacing: 1px;
            line-height: 1;
        }

        .kpi-card .kpi-sub {
            font-size: 11px;
            color: #555;
            margin-top: 6px;
        }

        .kpi-card.verde {
            --cor: #2ecc71;
        }

        .kpi-card.verde .kpi-icon {
            background: rgba(46, 204, 113, 0.1);
        }

        .kpi-card.verde .kpi-valor {
            color: #2ecc71;
        }

        .kpi-card.vermelho {
            --cor: #e74c3c;
        }

        .kpi-card.vermelho .kpi-icon {
            background: rgba(231, 76, 60, 0.1);
        }

        .kpi-card.vermelho .kpi-valor {
            color: #e74c3c;
        }

        .kpi-card.amarelo {
            --cor: #f1c40f;
        }

        .kpi-card.amarelo .kpi-icon {
            background: rgba(241, 196, 15, 0.1);
        }

        .kpi-card.amarelo .kpi-valor {
            color: #f1c40f;
        }

        .kpi-card.azul {
            --cor: #3498db;
        }

        .kpi-card.azul .kpi-icon {
            background: rgba(52, 152, 219, 0.1);
        }

        .kpi-card.azul .kpi-valor {
            color: #3498db;
        }

        /* ── SEÇÃO ALUNOS ── */
        .secao-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .secao-header h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 24px;
            letter-spacing: 1px;
            color: #fff;
        }

        .secao-header .badge-total {
            background: rgba(245, 197, 24, 0.1);
            border: 1px solid rgba(245, 197, 24, 0.2);
            color: #f5c518;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 999px;
        }

        .btn-novo-aluno {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            background: linear-gradient(90deg, #f5c518, #ffd84d);
            color: #111;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            transition: 0.2s;
            text-decoration: none;
        }

        .btn-novo-aluno:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(245, 197, 24, 0.3);
        }

        /* ── TABELA ── */
        .tabela-card {
            background: #161616;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            overflow: hidden;
        }

        .tabela-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        thead tr {
            background: #1a1a1a;
        }

        th {
            padding: 14px 20px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #f5c518;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        td {
            padding: 16px 20px;
            font-size: 14px;
            color: #ccc;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .td-nome {
            font-weight: 600;
            color: #f0f0f0;
        }

        .td-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f5c518, #ffd84d);
            color: #111;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            vertical-align: middle;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-badge.pago {
            background: rgba(46, 204, 113, 0.12);
            color: #2ecc71;
        }

        .status-badge.pendente {
            background: rgba(241, 196, 15, 0.12);
            color: #f1c40f;
        }

        .status-badge.atrasado {
            background: rgba(231, 76, 60, 0.12);
            color: #e74c3c;
        }

        .status-badge.sem-status {
            background: rgba(255, 255, 255, 0.06);
            color: #888;
        }

        .acoes a {
            color: #7aa2ff;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s;
        }

        .acoes a:hover {
            color: #fff;
        }

        .acoes span {
            color: #333;
            margin: 0 4px;
        }

        /* ── MOBILE OVERLAY ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 199;
        }

        .sidebar-overlay.ativo {
            display: block;
        }

        /* ── RESPONSIVO ── */
        @media (max-width: 1200px) {
            .kpi-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .main {
                padding: 24px;
            }

            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .sidebar {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .hamburger-btn {
                display: flex;
            }

            .topbar-usuario {
                display: none;
            }

            .sidebar {
                position: fixed;
                top: 64px;
                left: -260px;
                width: 240px;
                height: calc(100vh - 64px);
                z-index: 200;
                transition: left 0.3s ease;
                overflow-y: auto;
            }

            .sidebar.aberta {
                left: 0;
            }

            .main {
                padding: 20px 16px;
            }

            .page-header .saudacao {
                font-size: 30px;
            }

            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .kpi-card {
                padding: 16px;
            }

            .kpi-card .kpi-valor {
                font-size: 26px;
            }

            .coluna-email {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .page-header .saudacao {
                font-size: 24px;
            }

            .topbar {
                padding: 0 16px;
            }

            .main {
                padding: 16px 12px;
            }
        }
    </style>
</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-esquerda">
            <button class="hamburger-btn" id="hamburger-btn">
                <span></span><span></span><span></span>
            </button>
            <div class="topbar-logo-box">🏋️</div>
            <div class="topbar-titulo">
                <h1>GymFlow</h1>
                <p>Área Administrativa</p>
            </div>
        </div>
        <div class="topbar-direita">
            <div class="topbar-usuario">
                <div class="nome"><?php echo htmlspecialchars($_SESSION["nome"]); ?></div>
                <div class="cargo">Administrador</div>
            </div>
            <a href="../../logout.php" class="btn-sair">Sair</a>
        </div>
    </div>

    <!-- OVERLAY MOBILE -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="layout">

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <span class="sidebar-label">Menu</span>
            <a href="dashboard-admin.php" class="ativo"><span class="icon">📊</span> Dashboard</a>
            <a href="alunos.php"><span class="icon">👥</span> Alunos</a>
            <a href="pagamentos.php"><span class="icon">💳</span> Mensalidades</a>
            <a href="caixa.php"><span class="icon">💰</span> Caixa</a>
            <a href="#"><span class="icon">🏋️</span> Treinos</a>
            <span class="sidebar-label">Conta</span>
            <a href="../../logout.php"><span class="icon">🚪</span> Sair</a>
        </aside>

        <main class="main">

            <div class="page-header">
                <div class="saudacao">
                    <?php echo $saudacao; ?>, <span><?php echo htmlspecialchars(explode(' ', $_SESSION["nome"])[0]); ?></span> 👋
                </div>
                <p class="sub">Aqui está o resumo da sua academia hoje, <?php echo date("d/m/Y"); ?></p>
            </div>

            <!-- KPI CARDS -->
            <div class="kpi-grid">
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

            <!-- TABELA ALUNOS -->
            <div class="secao-header">
                <div style="display:flex;align-items:center;gap:12px;">
                    <h2>Alunos Cadastrados</h2>
                    <span class="badge-total"><?php echo $total_alunos; ?> alunos</span>
                </div>
                <a href="../../cadastro.php" class="btn-novo-aluno">+ Novo aluno</a>
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
                                    $vencimento = !empty($aluno["vencimento"]) ? date("d/m/Y", strtotime($aluno["vencimento"])) : "-";
                                    $inicial = strtoupper(substr($aluno["nome"], 0, 1));
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="td-avatar"><?php echo $inicial; ?></span>
                                            <span class="td-nome"><?php echo htmlspecialchars($aluno["nome"]); ?></span>
                                        </td>
                                        <td class="coluna-email"><?php echo htmlspecialchars($aluno["email"]); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $status; ?>"><?php echo $statusTexto; ?></span>
                                        </td>
                                        <td><?php echo $vencimento; ?></td>
                                        <td class="acoes">
                                            <a href="editar-aluno.php?id=<?php echo (int)$aluno["id"]; ?>">Editar</a>
                                            <span>|</span>
                                            <a href="/GymFlow-main/paginas/aluno/perfil.php?id=<?php echo (int)$aluno["id"]; ?>">Ver</a>
                                            <span>|</span>
                                            <a href="excluir-aluno.php?id=<?php echo (int)$aluno["id"]; ?>"
                                                onclick="return confirm('Excluir este aluno?')"
                                                style="color:#e74c3c;">Excluir</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;color:#555;padding:40px;">Nenhum aluno cadastrado.</td>
                                </tr>
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