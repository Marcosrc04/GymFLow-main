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

$mensagem = "";
$tipo_mensagem = "";
$id = intval($_GET["id"] ?? 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome  = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $nova_senha     = $_POST["nova_senha"] ?? "";
    $confirmar_nova = $_POST["confirmar_nova_senha"] ?? "";

    if (!empty($nova_senha)) {
        if ($nova_senha !== $confirmar_nova) {
            $mensagem = "As senhas não coincidem.";
            $tipo_mensagem = "erro";
        } else {
            $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $conexao->prepare("UPDATE usuarios SET nome=?, email=?, senha=? WHERE id=? AND tipo='aluno'");
            $stmt->bind_param("sssi", $nome, $email, $hash, $id);
            $stmt->execute();
            header("Location: dashboard-admin.php");
            exit();
        }
    } else {
        $stmt = $conexao->prepare("UPDATE usuarios SET nome=?, email=? WHERE id=? AND tipo='aluno'");
        $stmt->bind_param("ssi", $nome, $email, $id);
        $stmt->execute();
        header("Location: dashboard-admin.php");
        exit();
    }
}

$stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id=? AND tipo='aluno'");
$stmt->bind_param("i", $id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();
$inicial = strtoupper(substr($aluno["nome"] ?? "A", 0, 1));
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno | GymFlow</title>
    <link rel="shortcut icon" href="../../arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../arquivos/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: #0a0a0a;
        }

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

        .layout {
            display: flex;
            min-height: calc(100vh - 64px);
        }

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

        .main {
            flex: 1;
            padding: 36px;
            min-width: 0;
            overflow-x: hidden;
        }

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

        .editar-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
            align-items: start;
        }

        .aluno-card-id {
            background: #161616;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            padding: 28px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .aluno-card-id::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: linear-gradient(135deg, rgba(245, 197, 24, 0.1), transparent);
        }

        .aluno-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f5c518, #ffd84d);
            color: #111;
            font-size: 28px;
            font-family: 'Bebas Neue', sans-serif;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(245, 197, 24, 0.25);
            margin: 0 auto 14px;
            position: relative;
            z-index: 1;
        }

        .aluno-nome-card {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .aluno-email-card {
            color: #555;
            font-size: 12px;
            margin-bottom: 16px;
        }

        .aluno-id-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            background: rgba(245, 197, 24, 0.1);
            border: 1px solid rgba(245, 197, 24, 0.15);
            color: #f5c518;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
            padding: 10px 16px;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 10px;
            color: #aaa;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s;
            text-decoration: none;
            width: 100%;
            justify-content: center;
        }

        .back-link:hover {
            border-color: rgba(245, 197, 24, 0.3);
            color: #f5c518;
        }

        .form-card {
            background: #161616;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            padding: 32px;
        }

        .form-secao-titulo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 18px;
            letter-spacing: 1px;
            color: #fff;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-secao-sub {
            color: #555;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .form-secao-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .campo {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 16px;
        }

        .campo label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #666;
        }

        .campo input {
            background: #0f0f0f;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 13px 16px;
            color: #e0e0e0;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: 0.2s;
        }

        .campo input:focus {
            border-color: rgba(245, 197, 24, 0.5);
            box-shadow: 0 0 0 3px rgba(245, 197, 24, 0.08);
        }

        .campo input::placeholder {
            color: #3a3a3a;
        }

        .senha-hint {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            color: #555;
            margin-top: -8px;
            margin-bottom: 16px;
        }

        .alerta {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .alerta-erro {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.25);
            color: #e74c3c;
        }

        .btn-row {
            display: flex;
            gap: 12px;
            margin-top: 8px;
        }

        .btn-salvar {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px;
            background: linear-gradient(90deg, #f5c518, #ffd84d);
            color: #111;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: 0.2s;
        }

        .btn-salvar:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(245, 197, 24, 0.25);
        }

        .btn-cancelar {
            padding: 14px 20px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: #888;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .btn-cancelar:hover {
            border-color: rgba(255, 255, 255, 0.15);
            color: #ccc;
        }

        @media(max-width:900px) {
            .editar-grid {
                grid-template-columns: 1fr;
            }

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
        }

        @media(max-width:600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="topbar">
        <div class="topbar-esquerda">
            <button class="hamburger-btn" id="hamburger-btn"><span></span><span></span><span></span></button>
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

    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <span class="sidebar-label">Menu</span>
            <a href="dashboard-admin.php"><span class="icon">📊</span> Dashboard</a>
            <a href="alunos.php" class="ativo"><span class="icon">👥</span> Alunos</a>
            <a href="pagamentos.php"><span class="icon">💳</span> Mensalidades</a>
            <a href="caixa.php"><span class="icon">💰</span> Caixa</a>
            <a href="#"><span class="icon">🏋️</span> Treinos</a>
            <span class="sidebar-label">Conta</span>
            <a href="../../logout.php"><span class="icon">🚪</span> Sair</a>
        </aside>

        <main class="main">
            <div class="page-header">
                <div class="saudacao">Editar <span>Aluno</span></div>
                <p class="sub">Atualize as informações e senha do aluno</p>
            </div>

            <?php if (!empty($mensagem)): ?>
                <div class="alerta alerta-<?php echo $tipo_mensagem; ?>">✕ <?php echo htmlspecialchars($mensagem); ?></div>
            <?php endif; ?>

            <div class="editar-grid">

                <div class="aluno-card-id">
                    <div class="aluno-avatar"><?php echo $inicial; ?></div>
                    <div class="aluno-nome-card"><?php echo htmlspecialchars($aluno["nome"] ?? ""); ?></div>
                    <div class="aluno-email-card"><?php echo htmlspecialchars($aluno["email"] ?? ""); ?></div>
                    <div class="aluno-id-badge">ID #<?php echo str_pad($id, 3, "0", STR_PAD_LEFT); ?></div>
                    <a href="alunos.php" class="back-link">← Voltar para Alunos</a>
                </div>

                <div class="form-card">
                    <form action="" method="POST">

                        <div class="form-secao-titulo"><span>👤</span> Dados do Aluno</div>
                        <div class="form-secao-sub">Nome e e-mail de acesso ao sistema</div>
                        <hr class="form-secao-divider">
                        <div class="form-row">
                            <div class="campo">
                                <label>Nome completo</label>
                                <input type="text" name="nome" value="<?php echo htmlspecialchars($aluno["nome"] ?? ""); ?>" required>
                            </div>
                            <div class="campo">
                                <label>E-mail</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($aluno["email"] ?? ""); ?>" required>
                            </div>
                        </div>

                        <div class="form-secao-titulo" style="margin-top:8px"><span>🔒</span> Redefinir Senha</div>
                        <div class="form-secao-sub">Opcional — deixe em branco para manter a atual</div>
                        <hr class="form-secao-divider">
                        <div class="form-row">
                            <div class="campo">
                                <label>Nova senha</label>
                                <input type="password" name="nova_senha" placeholder="••••••••">
                            </div>
                            <div class="campo">
                                <label>Confirmar nova senha</label>
                                <input type="password" name="confirmar_nova_senha" placeholder="••••••••">
                            </div>
                        </div>
                        <div class="senha-hint">Deixe os campos de senha em branco para manter a senha atual do aluno.</div>

                        <div class="btn-row">
                            <a href="alunos.php" class="btn-cancelar">Cancelar</a>
                            <button type="submit" class="btn-salvar">Salvar Alterações</button>
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
        btn.addEventListener('click', () => {
            btn.classList.toggle('aberto');
            sidebar.classList.toggle('aberta');
            overlay.classList.toggle('ativo');
        });
        overlay.addEventListener('click', () => {
            btn.classList.remove('aberto');
            sidebar.classList.remove('aberta');
            overlay.classList.remove('ativo');
        });
    </script>
</body>

</html>