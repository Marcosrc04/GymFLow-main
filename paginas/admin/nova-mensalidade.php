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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aluno_id   = intval($_POST["aluno_id"]);
    $valor      = floatval($_POST["valor"]);
    $vencimento = $_POST["vencimento"];

    if ($aluno_id && $valor > 0 && $vencimento) {
        $stmt = $conexao->prepare("INSERT INTO pagamentos (aluno_id, valor, vencimento, status) VALUES (?, ?, ?, 'pendente')");
        $stmt->bind_param("ids", $aluno_id, $valor, $vencimento);
        if ($stmt->execute()) {
            header("Location: /GymFlow-main/paginas/admin/pagamentos.php");
            exit();
        } else {
            $mensagem      = "Erro ao salvar mensalidade.";
            $tipo_mensagem = "erro";
        }
    } else {
        $mensagem      = "Preencha todos os campos corretamente.";
        $tipo_mensagem = "erro";
    }
}

$alunos = $conexao->query("SELECT id, nome FROM usuarios WHERE tipo = 'aluno' ORDER BY nome ASC");
if (!$alunos) die("Erro: " . $conexao->error);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Mensalidade | GymFlow Admin</title>
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
        <a href="/GymFlow-main/paginas/admin/pagamentos.php" class="ativo"><span class="icon">💳</span> Mensalidades</a>
        <a href="/GymFlow-main/paginas/admin/caixa.php"><span class="icon">💰</span> Caixa</a>
        <a href="#"><span class="icon">🏋️</span> Treinos</a>
        <span class="sidebar-label">Conta</span>
        <a href="/GymFlow-main/paginas/aluno/perfil.php"><span class="icon">👤</span> Meu Perfil</a>
        <a href="/GymFlow-main/logout.php"><span class="icon">🚪</span> Sair</a>
    </aside>

    <main class="main">
        <div class="page-header">
            <div class="saudacao">Nova <span>Mensalidade</span></div>
            <p class="sub">Cadastre uma cobrança para um aluno</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alerta alerta-<?php echo $tipo_mensagem; ?>">✕ <?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <div class="form-wrap">
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
                                    <option value="<?php echo (int)$aluno["id"]; ?>"><?php echo htmlspecialchars($aluno["nome"]); ?></option>
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
                        <a href="/GymFlow-main/paginas/admin/pagamentos.php" class="btn-cancelar">Cancelar</a>
                        <button type="submit" class="btn-salvar">Registrar Mensalidade</button>
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