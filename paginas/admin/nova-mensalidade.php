<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["id"])) { header("Location: ../../login.php"); exit(); }
if ($_SESSION["tipo"] != "admin") { header("Location: ../../login.php"); exit(); }

include("../../includes/conexao.php");

$mensagem = "";
$tipo_mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aluno_id  = intval($_POST["aluno_id"]);
    $valor     = floatval($_POST["valor"]);
    $vencimento = $_POST["vencimento"];

    if ($aluno_id && $valor > 0 && $vencimento) {
        $stmt = $conexao->prepare("INSERT INTO pagamentos (aluno_id, valor, vencimento, status) VALUES (?, ?, ?, 'pendente')");
        $stmt->bind_param("ids", $aluno_id, $valor, $vencimento);
        if ($stmt->execute()) {
            header("Location: pagamentos.php");
            exit();
        } else {
            $mensagem = "Erro ao salvar mensalidade.";
            $tipo_mensagem = "erro";
        }
    } else {
        $mensagem = "Preencha todos os campos corretamente.";
        $tipo_mensagem = "erro";
    }
}

$sql = "SELECT id, nome FROM usuarios WHERE tipo = 'aluno' ORDER BY nome ASC";
$alunos = $conexao->query($sql);
if (!$alunos) die("Erro: " . $conexao->error);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Mensalidade | GymFlow</title>
    <link rel="shortcut icon" href="../../arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../arquivos/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #0a0a0a; }
        .topbar { width:100%;background:#111;border-bottom:1px solid rgba(255,204,0,0.1);padding:0 32px;height:64px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100; }
        .topbar-esquerda { display:flex;align-items:center;gap:14px; }
        .topbar-logo-box { width:38px;height:38px;background:linear-gradient(135deg,#f5c518,#ffd84d);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px; }
        .topbar-titulo h1 { color:#fff;font-size:17px;font-weight:700;line-height:1.2; }
        .topbar-titulo p { color:#888;font-size:11px; }
        .topbar-direita { display:flex;align-items:center;gap:16px; }
        .topbar-usuario .nome { color:#f5c518;font-size:14px;font-weight:600; }
        .topbar-usuario .cargo { color:#888;font-size:11px; }
        .btn-sair { border:1px solid rgba(245,197,24,0.4);color:#f5c518;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:500;transition:0.2s; }
        .btn-sair:hover { background:#f5c518;color:#111; }
        .hamburger-btn { display:none;flex-direction:column;gap:5px;cursor:pointer;padding:6px;background:#1e1e1e;border-radius:8px;border:1px solid rgba(255,204,0,0.15); }
        .hamburger-btn span { display:block;width:20px;height:2px;background:#f5c518;border-radius:2px;transition:0.3s; }
        .hamburger-btn.aberto span:nth-child(1) { transform:translateY(7px) rotate(45deg); }
        .hamburger-btn.aberto span:nth-child(2) { opacity:0; }
        .hamburger-btn.aberto span:nth-child(3) { transform:translateY(-7px) rotate(-45deg); }
        .layout { display:flex;min-height:calc(100vh - 64px); }
        .sidebar { width:240px;background:#111;border-right:1px solid rgba(255,204,0,0.08);padding:28px 16px;flex-shrink:0;display:flex;flex-direction:column;gap:6px;background-image:url(../../arquivos/imagem/listras-da-barra.png);background-size:cover;background-position:center; }
        .sidebar-label { font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#555;padding:0 12px;margin:16px 0 8px; }
        .sidebar-label:first-child { margin-top:0; }
        .sidebar a { display:flex;align-items:center;gap:10px;padding:11px 14px;border-radius:10px;color:#aaa;font-size:14px;font-weight:500;transition:0.2s;text-decoration:none; }
        .sidebar a:hover { background:rgba(245,197,24,0.08);color:#f5c518; }
        .sidebar a.ativo { background:rgba(245,197,24,0.12);color:#f5c518;border:1px solid rgba(245,197,24,0.2); }
        .sidebar a .icon { font-size:16px;width:20px;text-align:center; }
        .sidebar-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:199; }
        .sidebar-overlay.ativo { display:block; }
        .main { flex:1;padding:36px;min-width:0;overflow-x:hidden; }
        .page-header { margin-bottom:32px; }
        .page-header .saudacao { font-family:'Bebas Neue',sans-serif;font-size:42px;line-height:1;color:#fff;letter-spacing:1px; }
        .page-header .saudacao span { color:#f5c518; }
        .page-header .sub { color:#666;font-size:14px;margin-top:6px; }

        .nova-mens-wrap { max-width: 680px; }

        .form-card { background:#161616;border:1px solid rgba(255,255,255,0.06);border-radius:20px;padding:36px; }

        .resumo-strip {
            display: flex; gap: 12px; margin-bottom: 28px;
        }
        .resumo-item {
            flex: 1; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px; padding: 14px 16px;
            display: flex; align-items: center; gap: 12px;
        }
        .resumo-item .ri-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(245,197,24,0.1); display: flex; align-items: center;
            justify-content: center; font-size: 16px; flex-shrink: 0;
        }
        .resumo-item .ri-label { color: #555; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .resumo-item .ri-value { color: #e0e0e0; font-size: 14px; font-weight: 600; margin-top: 2px; }

        .form-secao-titulo { font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:#fff;margin-bottom:4px;display:flex;align-items:center;gap:8px; }
        .form-secao-sub { color:#555;font-size:12px;margin-bottom:16px; }
        .form-secao-divider { border:none;border-top:1px solid rgba(255,255,255,0.05);margin-bottom:20px; }

        .form-row-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px; }
        .campo { display:flex;flex-direction:column;gap:7px;margin-bottom:0; }
        .campo label { font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#666; }
        .campo input, .campo select {
            background:#0f0f0f;border:1px solid rgba(255,255,255,0.07);border-radius:12px;
            padding:13px 16px;color:#e0e0e0;font-size:14px;
            font-family:'DM Sans',sans-serif;outline:none;transition:0.2s;
            appearance:none; -webkit-appearance:none;
        }
        .campo select {
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23f5c518' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat:no-repeat;background-position:right 16px center;padding-right:40px;
        }
        .campo input:focus, .campo select:focus { border-color:rgba(245,197,24,0.5);box-shadow:0 0 0 3px rgba(245,197,24,0.08); }
        .campo select option { background:#1a1a1a; }
        .campo input::placeholder { color:#3a3a3a; }
        .campo input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(0.6) sepia(1) saturate(3) hue-rotate(5deg); }

        .alerta { display:flex;align-items:center;gap:10px;padding:13px 16px;border-radius:12px;font-size:13px;font-weight:600;margin-bottom:20px; }
        .alerta-erro { background:rgba(231,76,60,0.1);border:1px solid rgba(231,76,60,0.25);color:#e74c3c; }

        .btn-row { display:flex;gap:12px;margin-top:24px; }
        .btn-salvar { flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:15px;background:linear-gradient(90deg,#f5c518,#ffd84d);color:#111;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:0.2s; }
        .btn-salvar:hover { transform:translateY(-1px);box-shadow:0 8px 24px rgba(245,197,24,0.25); }
        .btn-cancelar { padding:15px 20px;background:transparent;border:1px solid rgba(255,255,255,0.08);border-radius:12px;color:#888;font-size:14px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif;transition:0.2s;text-decoration:none;display:flex;align-items:center; }
        .btn-cancelar:hover { border-color:rgba(255,255,255,0.15);color:#ccc; }

        @media(max-width:768px){
            .hamburger-btn { display:flex; }
            .topbar-usuario { display:none; }
            .sidebar { position:fixed;top:64px;left:-260px;width:240px;height:calc(100vh - 64px);z-index:200;transition:left 0.3s ease;overflow-y:auto; }
            .sidebar.aberta { left:0; }
            .main { padding:20px 16px; }
            .form-row-3 { grid-template-columns:1fr; }
            .resumo-strip { flex-direction:column; }
            .form-card { padding:20px; }
        }
    </style>
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
        <a href="../../logout.php" class="btn-sair">Sair</a>
    </div>
</div>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

<div class="layout">
    <aside class="sidebar" id="sidebar">
        <span class="sidebar-label">Menu</span>
        <a href="dashboard-admin.php"><span class="icon">📊</span> Dashboard</a>
        <a href="alunos.php"><span class="icon">👥</span> Alunos</a>
        <a href="pagamentos.php" class="ativo"><span class="icon">💳</span> Mensalidades</a>
        <a href="caixa.php"><span class="icon">💰</span> Caixa</a>
        <a href="#"><span class="icon">🏋️</span> Treinos</a>
        <span class="sidebar-label">Conta</span>
        <a href="../../logout.php"><span class="icon">🚪</span> Sair</a>
    </aside>

    <main class="main">
        <div class="page-header">
            <div class="saudacao">Nova <span>Mensalidade</span></div>
            <p class="sub">Cadastre uma cobrança para um aluno</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alerta alerta-<?php echo $tipo_mensagem; ?>">✕ <?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <div class="nova-mens-wrap">

            <div class="resumo-strip">
                <div class="resumo-item">
                    <div class="ri-icon">💳</div>
                    <div><div class="ri-label">Tipo</div><div class="ri-value">Mensalidade</div></div>
                </div>
                <div class="resumo-item">
                    <div class="ri-icon">📅</div>
                    <div><div class="ri-label">Status inicial</div><div class="ri-value">Pendente</div></div>
                </div>
                <div class="resumo-item">
                    <div class="ri-icon">📋</div>
                    <div><div class="ri-label">Criado em</div><div class="ri-value"><?php echo date("d/m/Y"); ?></div></div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-secao-titulo"><span>💳</span> Dados da Mensalidade</div>
                <div class="form-secao-sub">Preencha as informações abaixo para registrar a cobrança</div>
                <hr class="form-secao-divider">

                <form action="" method="POST">
                    <div class="form-row-3">
                        <div class="campo">
                            <label>Aluno</label>
                            <select name="aluno_id" required>
                                <option value="">Selecione...</option>
                                <?php while ($aluno = $alunos->fetch_assoc()): ?>
                                    <option value="<?php echo (int)$aluno["id"]; ?>">
                                        <?php echo htmlspecialchars($aluno["nome"]); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="campo">
                            <label>Valor (R$)</label>
                            <input type="number" step="0.01" name="valor" min="0.01" placeholder="150,00" required>
                        </div>
                        <div class="campo">
                            <label>Vencimento</label>
                            <input type="date" name="vencimento" required>
                        </div>
                    </div>

                    <div class="btn-row">
                        <a href="pagamentos.php" class="btn-cancelar">Cancelar</a>
                        <button type="submit" class="btn-salvar">Registrar Mensalidade</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
const btn = document.getElementById('hamburger-btn');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebar-overlay');
btn.addEventListener('click', () => { btn.classList.toggle('aberto'); sidebar.classList.toggle('aberta'); overlay.classList.toggle('ativo'); });
overlay.addEventListener('click', () => { btn.classList.remove('aberto'); sidebar.classList.remove('aberta'); overlay.classList.remove('ativo'); });
</script>
</body>
</html>