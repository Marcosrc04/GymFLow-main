<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {
    header("Location: ../../login.php");
    exit();
}

include("../../includes/conexao.php");

$resultado_alunos = $conexao->query("SELECT id, nome, email, tipo FROM usuarios WHERE tipo = 'aluno' ORDER BY nome ASC");

if (!$resultado_alunos) {
    die("Erro na consulta: " . $conexao->error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos | GymFlow Admin</title>
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
            <div class="saudacao">Alunos <span>Cadastrados</span></div>
            <p class="sub">Gerencie os alunos cadastrados no sistema</p>
        </div>

        <div class="secao-header">
            <div class="secao-header-esquerda">
                <h2>Lista de Alunos</h2>
            </div>
            <a href="/GymFlow-main/cadastro.php" class="btn-novo-aluno">+ Novo aluno</a>
        </div>

        <div class="tabela-card">
            <div class="tabela-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th class="coluna-email">E-mail</th>
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_alunos->num_rows > 0): ?>
                            <?php while ($aluno = $resultado_alunos->fetch_assoc()): ?>
                                <tr>
                                    <td class="td-nome"><?php echo htmlspecialchars($aluno["nome"]); ?></td>
                                    <td class="coluna-email"><?php echo htmlspecialchars($aluno["email"]); ?></td>
                                    <td><?php echo ucfirst(htmlspecialchars($aluno["tipo"])); ?></td>
                                    <td class="coluna-acoes">
                                        <a href="/GymFlow-main/paginas/admin/editar-aluno.php?id=<?php echo (int)$aluno["id"]; ?>">Editar</a>
                                        <span>|</span>
                                        <a href="/GymFlow-main/paginas/admin/excluir-aluno.php?id=<?php echo (int)$aluno["id"]; ?>" onclick="return confirm('Tem certeza que deseja excluir este aluno?')" class="link-excluir">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="td-vazio">Nenhum aluno encontrado.</td></tr>
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