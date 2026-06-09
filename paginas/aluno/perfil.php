<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"])) {
    header("Location: ../../login.php");
    exit();
}

include("../../includes/conexao.php");

$id            = (int)$_SESSION["id"];
$mensagem      = "";
$tipo_mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome       = trim($_POST["nome"] ?? "");
    $email      = trim($_POST["email"] ?? "");
    $nova_senha = $_POST["nova_senha"] ?? "";
    $confirmar  = $_POST["confirmar_nova_senha"] ?? "";

    if (!empty($nova_senha)) {
        if ($nova_senha !== $confirmar) {
            $mensagem      = "As senhas não coincidem.";
            $tipo_mensagem = "erro";
        } else {
            $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $conexao->prepare("UPDATE usuarios SET nome=?, email=?, senha=? WHERE id=?");
            $stmt->bind_param("sssi", $nome, $email, $hash, $id);
            $stmt->execute();
            $_SESSION["nome"] = $nome;
            $mensagem         = "Perfil atualizado com sucesso!";
            $tipo_mensagem    = "sucesso";
        }
    } else {
        $stmt = $conexao->prepare("UPDATE usuarios SET nome=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $nome, $email, $id);
        $stmt->execute();
        $_SESSION["nome"] = $nome;
        $mensagem         = "Perfil atualizado com sucesso!";
        $tipo_mensagem    = "sucesso";
    }
}

$stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario     = $stmt->get_result()->fetch_assoc();
$inicial     = strtoupper(substr($usuario["nome"], 0, 1));
$isAdmin     = $_SESSION["tipo"] === "admin";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | GymFlow</title>
    <link rel="shortcut icon" href="../../arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../arquivos/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="topbar">
    <div class="topbar-esquerda">
        <button class="hamburger-btn" id="hamburger-btn"><span></span><span></span><span></span></button>
        <div class="topbar-logo-box">🏋️</div>
        <div class="topbar-titulo">
            <h1>GymFlow</h1>
            <p><?php echo $isAdmin ? "Área Administrativa" : "Área do Aluno"; ?></p>
        </div>
    </div>
    <div class="topbar-direita">
        <div class="topbar-avatar">
            <?php if (!empty($usuario["foto"])): ?>
                <img src="../../arquivos/imagem/perfis/<?php echo htmlspecialchars($usuario["foto"]); ?>" alt="">
            <?php else: ?>
                <?php echo $inicial; ?>
            <?php endif; ?>
        </div>
        <div class="topbar-usuario">
            <div class="nome"><?php echo htmlspecialchars($_SESSION["nome"]); ?></div>
            <div class="cargo"><?php echo ucfirst($_SESSION["tipo"]); ?></div>
        </div>
        <a href="/GymFlow-main/logout.php" class="btn-sair">Sair</a>
    </div>
</div>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

<div class="layout">
    <aside class="sidebar" id="sidebar">
        <?php if ($isAdmin): ?>
            <span class="sidebar-label">Menu</span>
            <a href="/GymFlow-main/paginas/admin/dashboard-admin.php"><span class="icon">📊</span> Dashboard</a>
            <a href="/GymFlow-main/paginas/aluno/gerar_treino_com_ia/gerar-treino.php"><span class="icon">🏋️</span> Meus Treinos</a>
            <a href="/GymFlow-main/paginas/admin/alunos.php"><span class="icon">👥</span> Alunos</a>
            <a href="/GymFlow-main/paginas/admin/pagamentos.php"><span class="icon">💳</span> Mensalidades</a>
            <a href="/GymFlow-main/paginas/admin/caixa.php"><span class="icon">💰</span> Caixa</a>
            <a href="#"><span class="icon">🏋️</span> Treinos</a>
            <span class="sidebar-label">Conta</span>
            <a href="/GymFlow-main/paginas/aluno/perfil.php" class="ativo"><span class="icon">👤</span> Meu Perfil</a>
            <a href="/GymFlow-main/logout.php"><span class="icon">🚪</span> Sair</a>
        <?php else: ?>
            <span class="sidebar-label">Menu</span>
            <a href="/GymFlow-main/paginas/aluno/dashboard-aluno.php"><span class="icon">📊</span> Dashboard</a>
            <a href="/GymFlow-main/paginas/aluno/gerar_treino_com_ia/gerar-treino.php"><span class="icon">🏋️</span> Meus Treinos</a>
            <a href="/GymFlow-main/paginas/aluno/caixa.php"><span class="icon">💳</span> Mensalidades</a>
            <span class="sidebar-label">Conta</span>
            <a href="/GymFlow-main/paginas/aluno/perfil.php" class="ativo"><span class="icon">👤</span> Meu Perfil</a>
            <a href="/GymFlow-main/logout.php"><span class="icon">🚪</span> Sair</a>
        <?php endif; ?>
    </aside>

    <main class="main">
        <div class="page-header">
            <div class="saudacao">Meu <span>Perfil</span></div>
            <p class="sub">Gerencie suas informações pessoais e senha de acesso</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alerta alerta-<?php echo $tipo_mensagem; ?>">
                <?php echo $tipo_mensagem === "sucesso" ? "✓" : "✕"; ?>
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <div class="perfil-grid">
            <div class="perfil-card-id">
                <div class="perfil-avatar-wrap">
                    <?php if (!empty($usuario["foto"])): ?>
                        <div class="perfil-avatar"><img src="../../arquivos/imagem/perfis/<?php echo htmlspecialchars($usuario["foto"]); ?>" alt=""></div>
                    <?php else: ?>
                        <div class="perfil-avatar"><?php echo $inicial; ?></div>
                    <?php endif; ?>
                    <div class="perfil-avatar-dot"></div>
                </div>
                <div class="perfil-nome"><?php echo htmlspecialchars($usuario["nome"]); ?></div>
                <div class="perfil-email"><?php echo htmlspecialchars($usuario["email"]); ?></div>
                <div class="perfil-tipo-badge"><?php echo ucfirst($usuario["tipo"]); ?></div>
                <div class="perfil-stats">
                    <div class="perfil-stat">
                        <div class="sv"><?php echo date("Y") - 2023; ?>+</div>
                        <div class="sl">Anos</div>
                    </div>
                    <div class="perfil-stat">
                        <div class="sv"><?php echo date("m"); ?></div>
                        <div class="sl">Meses</div>
                    </div>
                    <div class="perfil-stat">
                        <div class="sv">#<?php echo str_pad($usuario["id"], 3, "0", STR_PAD_LEFT); ?></div>
                        <div class="sl">ID</div>
                    </div>
                </div>
            </div>

            <div class="perfil-form-card">
                <form action="" method="POST">
                    <div class="form-secao">
                        <div class="form-secao-titulo"><span>👤</span> Informações Pessoais</div>
                        <div class="form-secao-sub">Atualize seu nome e e-mail de acesso</div>
                        <hr class="form-secao-divider">
                        <div class="form-row">
                            <div class="campo">
                                <label>Nome completo</label>
                                <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario["nome"]); ?>" required>
                            </div>
                            <div class="campo">
                                <label>E-mail</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($usuario["email"]); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-secao">
                        <div class="form-secao-titulo"><span>🔒</span> Segurança</div>
                        <div class="form-secao-sub">Deixe em branco para manter a senha atual</div>
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
                        <div class="senha-hint">A senha deve ter pelo menos 8 caracteres. Deixe em branco para não alterar.</div>
                    </div>

                    <button type="submit" class="btn-salvar">Salvar Alterações</button>
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