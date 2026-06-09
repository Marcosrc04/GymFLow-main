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

$resultado_pagamentos = $conexao->query("SELECT p.id, u.nome, p.valor, p.vencimento, p.data_pagamento, p.status
    FROM pagamentos p
    INNER JOIN usuarios u ON p.aluno_id = u.id
    WHERE u.tipo = 'aluno'
    ORDER BY p.vencimento ASC");

if (!$resultado_pagamentos) {
    die("Erro na consulta: " . $conexao->error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensalidades | GymFlow Admin</title>
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
            <div class="saudacao">Mensali<span>dades</span></div>
            <p class="sub">Gerencie os pagamentos dos alunos</p>
        </div>

        <div class="secao-header">
            <div class="secao-header-esquerda">
                <h2>Lista de Pagamentos</h2>
            </div>
            <a href="/GymFlow-main/paginas/admin/nova-mensalidade.php" class="btn-novo-aluno">+ Nova mensalidade</a>
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
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_pagamentos->num_rows > 0): ?>
                            <?php while ($pagamento = $resultado_pagamentos->fetch_assoc()): ?>
                                <?php
                                $status = $pagamento["status"];
                                if ($status != "pago" && $pagamento["vencimento"] < date("Y-m-d")) {
                                    $status = "atrasado";
                                }
                                $dataPagamento = "-";
                                if (!empty($pagamento["data_pagamento"]) && $pagamento["data_pagamento"] != "0000-00-00") {
                                    $dataPagamento = date("d/m/Y", strtotime($pagamento["data_pagamento"]));
                                }
                                ?>
                                <tr>
                                    <td class="td-nome"><?php echo htmlspecialchars($pagamento["nome"]); ?></td>
                                    <td>R$ <?php echo number_format((float)$pagamento["valor"], 2, ',', '.'); ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($pagamento["vencimento"])); ?></td>
                                    <td><?php echo $dataPagamento; ?></td>
                                    <td><span class="status <?php echo htmlspecialchars($status); ?>"><?php echo ucfirst($status); ?></span></td>
                                    <td class="coluna-acoes">
                                        <?php if ($pagamento["status"] != "pago"): ?>
                                            <a href="/GymFlow-main/paginas/admin/marcar-pago.php?id=<?php echo (int)$pagamento["id"]; ?>">Marcar como pago</a>
                                        <?php else: ?>
                                            ✔ Pago
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="td-vazio">Nenhuma mensalidade encontrada.</td></tr>
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