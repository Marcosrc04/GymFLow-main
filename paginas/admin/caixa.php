<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "aluno") {
    header("Location: ../../login.php");
    exit();
}

include("../../includes/conexao.php");

date_default_timezone_set("America/Sao_Paulo");

$aluno_id  = (int)$_SESSION["id"];
$resultado = $conexao->query("SELECT * FROM pagamentos WHERE aluno_id = $aluno_id ORDER BY vencimento DESC");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensalidades | GymFlow</title>
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
        <a href="/GymFlow-main/paginas/admin/dashboard-admin.php"><span class="icon">📊</span> Dashboard</a>
        <a href="/GymFlow-main/paginas/admin/alunos.php"><span class="icon">👥</span> Alunos</a>
        <a href="/GymFlow-main/paginas/admin/pagamentos.php"><span class="icon">💳</span> Mensalidades</a>
        <a href="#"><span class="icon">🏋️</span> Treinos</a>
        <span class="sidebar-label">Conta</span>
        <a href="/GymFlow-main/paginas/aluno/perfil.php"><span class="icon">👤</span> Meu Perfil</a>
        <a href="/GymFlow-main/logout.php"><span class="icon">🚪</span> Sair</a>
    </aside>

    <main class="main">
        <div class="page-header">
            <div class="saudacao">Minhas <span>Mensalidades</span></div>
            <p class="sub">Acompanhe e pague suas mensalidades</p>
        </div>

        <?php if (isset($_GET["sucesso"])): ?>
            <div class="alerta alerta-sucesso">✅ Pagamento realizado com sucesso! Sua mensalidade foi atualizada.</div>
        <?php endif; ?>
        <?php if (isset($_GET["pendente"])): ?>
            <div class="alerta alerta-aviso">⏳ Pagamento pendente. Aguardando confirmação.</div>
        <?php endif; ?>
        <?php if (isset($_GET["falha"])): ?>
            <div class="alerta alerta-erro">❌ Pagamento não concluído. Tente novamente.</div>
        <?php endif; ?>

        <div class="tabela-card">
            <div class="tabela-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Pagamento</th>
                            <th>Método</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($p = $resultado->fetch_assoc()): ?>
                                <?php
                                $status = $p["status"];
                                if ($status != "pago" && $p["vencimento"] < date("Y-m-d")) {
                                    $status = "atrasado";
                                }
                                $dataPag = (!empty($p["data_pagamento"]) && $p["data_pagamento"] != "0000-00-00")
                                    ? date("d/m/Y", strtotime($p["data_pagamento"])) : "-";
                                $metodo  = !empty($p["metodo_pagamento"]) ? ucfirst($p["metodo_pagamento"]) : "-";
                                ?>
                                <tr>
                                    <td><?php echo date("d/m/Y", strtotime($p["vencimento"])); ?></td>
                                    <td>R$ <?php echo number_format((float)$p["valor"], 2, ',', '.'); ?></td>
                                    <td><span class="status <?php echo htmlspecialchars($status); ?>"><?php echo ucfirst($status); ?></span></td>
                                    <td><?php echo $dataPag; ?></td>
                                    <td><?php echo htmlspecialchars($metodo); ?></td>
                                    <td class="coluna-acoes">
                                        <?php if ($status != "pago"): ?>
                                            <a href="/GymFlow-main/paginas/aluno/processar-pagamento.php?id=<?php echo (int)$p["id"]; ?>" class="btn-pagar">💳 Pagar agora</a>
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