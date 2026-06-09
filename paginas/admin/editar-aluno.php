<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {
    header("Location: ../../login.php");
    exit();
}

include("../../includes/conexao.php");

$mensagem      = "";
$tipo_mensagem = "";
$id            = intval($_GET["id"] ?? 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome           = trim($_POST["nome"] ?? "");
    $email          = trim($_POST["email"] ?? "");
    $nova_senha     = $_POST["nova_senha"] ?? "";
    $confirmar_nova = $_POST["confirmar_nova_senha"] ?? "";

    if (!empty($nova_senha)) {
        if ($nova_senha !== $confirmar_nova) {
            $mensagem      = "As senhas não coincidem.";
            $tipo_mensagem = "erro";
        } else {
            $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $conexao->prepare("UPDATE usuarios SET nome=?, email=?, senha=? WHERE id=? AND tipo='aluno'");
            $stmt->bind_param("sssi", $nome, $email, $hash, $id);
            $stmt->execute();
            header("Location: /GymFlow-main/paginas/admin/dashboard-admin.php");
            exit();
        }
    } else {
        $stmt = $conexao->prepare("UPDATE usuarios SET nome=?, email=? WHERE id=? AND tipo='aluno'");
        $stmt->bind_param("ssi", $nome, $email, $id);
        $stmt->execute();
        header("Location: /GymFlow-main/paginas/admin/dashboard-admin.php");
        exit();
    }
}

$stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id=? AND tipo='aluno'");
$stmt->bind_param("i", $id);
$stmt->execute();
$aluno   = $stmt->get_result()->fetch_assoc();
$inicial = strtoupper(substr($aluno["nome"] ?? "A", 0, 1));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno | GymFlow Admin</title>
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
        <a href="/GymFlow-main/paginas/admin/alunos.php" class="ativo"><span class="icon">👥</span> Alunos</a>
        <a href="/GymFlow-main/paginas/admin/pagamentos.php"><span class="icon">💳</span> Mensalidades</a>
        <a href="/GymFlow-main/paginas/admin/caixa.php"><span class="icon">💰</span> Caixa</a>
        <a href="#"><span class="icon">🏋️</span> Treinos</a>
        <span class="sidebar-label">Conta</span>
        <a href="/GymFlow-main/paginas/aluno/perfil.php"><span class="icon">👤</span> Meu Perfil</a>
        <a href="/GymFlow-main/logout.php"><span class="icon">🚪</span> Sair</a>
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
                <a href="/GymFlow-main/paginas/admin/alunos.php" class="back-link">← Voltar para Alunos</a>
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
                        <a href="/GymFlow-main/paginas/admin/alunos.php" class="btn-cancelar">Cancelar</a>
                        <button type="submit" class="btn-salvar">Salvar Alterações</button>
                    </div>
                </form>
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