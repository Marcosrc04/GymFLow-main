<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"])) {
    header("Location: ../../login.php");
    exit();
}
?>

<?php
date_default_timezone_set("America/Sao_Paulo");

$hora = date("H");

if ($hora < 12) {
    $saudacao = "Bom dia";
} elseif ($hora < 18) {
    $saudacao = "Boa tarde";
} else {
    $saudacao = "Boa noite";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Aluno | GymFlow</title>

    <link rel="shortcut icon" href="../../arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../arquivos/css/style.css">
</head>
<header class="topo-header">
    <div class="container-topo">

        <div class="topo-esquerda">
            <div class="logo-box">🏋️</div>

            <div class="titulo">
                <h1>GymFlow</h1>
                <p>Área do Aluno</p>
            </div>
        </div>
        <div class="topo-foto-perfil">
            <?php if (!empty($_SESSION["foto"])) { ?>
                <img src="../../arquivos/imagem/perfis/<?php echo htmlspecialchars($_SESSION["foto"]); ?>"
                    class="foto-header">
            <?php } else { ?>
                <div class="avatar-letra-header">
                    <?php echo strtoupper(substr($_SESSION["nome"], 0, 1)); ?>
                </div>
            <?php } ?>
        </div>
        <div class="topo-direita">
            <div class="usuario-info">
                <p class="nome-usuario">
                    <?php echo $_SESSION["nome"]; ?>
                </p>
                <p class="tipo-usuario">Aluno</p>
            </div>

            <a href="../../logout.php" class="btn-sair">Sair</a>
        </div>

    </div>
</header>

<body>

    <div class="layout-dashboard">

        <aside class="sidebar">
            <div class="topo-sidebar">
                <img src="../../arquivos/imagem/1312fedc-983d-42d0-80df-ada4981193f2.png" alt="Logo GymFlow"
                    class="logo-sidebar">
                <h2>GymFlow</h2>
            </div>

            <nav class="menu-sidebar">
                <a href="#">Início</a>
                <a href="gerar_treino_com_ia/gerar-treino.php">Meus Treinos</a>
                <a href="#">Mensalidade</a>
                <a href="#">Meu Perfil</a>
                <a href="../../logout.php">Sair</a>
            </nav>
        </aside>

        <main class="conteudo-dashboard">
            <header class="topo-dashboard">
                <div class="header-esquerda">
                    <h1 class="titulo-destaque">
                        <?php echo $saudacao; ?>,
                        <span class="nome-usuario">
                            <?php echo htmlspecialchars($_SESSION["nome"]); ?>
                        </span> 👋
                    </h1>

                    <p class="subtitulo-dashboard">
                        Acompanhe seu desenvolvimento, Mensalidade e fique sempre Atualizado 💪
                </div>

                <div class="header-direita">

                    <div class="usuario-logado-box">

                        <?php if (!empty($_SESSION["foto"])) { ?>
                            <img src="../../arquivos/imagem/perfis/<?php echo htmlspecialchars($_SESSION["foto"]); ?>"
                                class="foto-usuario-topo">
                        <?php } else { ?>
                            <div class="avatar-letra">
                                <?php echo strtoupper(substr($_SESSION["nome"], 0, 1)); ?>
                            </div>
                        <?php } ?>
                        <div class="dados-usuario-topo">
                            <h3>
                                <?php echo htmlspecialchars($_SESSION["nome"]); ?>
                            </h3>
                            <span class="badge-tipo">
                                <?php echo ucfirst(htmlspecialchars($_SESSION["tipo"])); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <section class="cards-resumo">
                <div class="card-resumo">
                    <h3>Plano Atual</h3>
                    <p>Plano Premium</p>
                </div>

                <div class="card-resumo">
                    <h3>Mensalidade</h3>
                    <p>Em dia</p>
                </div>

                <div class="card-resumo">
                    <h3>IMC</h3>
                    <p>23.4</p>
                </div>

                <div class="card-resumo">
                    <h3>Treino Atual</h3>
                    <p>A - Peito e Tríceps</p>
                </div>
            </section>

            <section class="blocos-dashboard">
                <div class="bloco-dashboard">
                    <h2>Treino de Hoje</h2>
                    <ul>
                        <li>Supino reto - 4x10</li>
                        <li>Supino inclinado - 3x12</li>
                        <li>Crucifixo - 3x12</li>
                        <li>Tríceps corda - 3x15</li>
                    </ul>
                </div>

                <div class="bloco-dashboard">
                    <h2>Status da Mensalidade</h2>
                    <p>Situação: <strong>Pago</strong></p>
                    <p>Vencimento: 15/04/2026</p>
                </div>
            </section>
        </main>

    </div>

</body>

</html>