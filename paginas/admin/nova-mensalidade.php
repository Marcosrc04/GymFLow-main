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

$sql = "SELECT id, nome FROM usuarios WHERE tipo = 'aluno' ORDER BY nome ASC";
$alunos = $conexao->query($sql);

if (!$alunos) {
    die("Erro ao buscar alunos: " . $conexao->error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Mensalidade | GymFlow</title>
    <link rel="shortcut icon" href="../../arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../arquivos/css/style.css">
</head>
<body>

<div class="layout-dashboard">

    <aside class="sidebar">
        <div class="topo-sidebar">
            <img src="../../arquivos/imagem/1312fedc-983d-42d0-80df-ada4981193f2.png" alt="Logo GymFlow" class="logo-sidebar">
            <h2>GymFlow</h2>
        </div>

        <nav class="menu-sidebar">
            <a href="dashboard-admin.php">Dashboard</a>
            <a href="alunos.php">Alunos</a>
            <a href="pagamentos.php">Mensalidades</a>
            <a href="#">Treinos</a>
            <a href="../../logout.php">Sair</a>
        </nav>
    </aside>

    <main class="conteudo-dashboard">
        <header class="topo-dashboard">
            <h1>Nova Mensalidade</h1>
            <p>Cadastre uma mensalidade para um aluno</p>
        </header>

        <section class="blocos-dashboard-admin">
            <div class="bloco-dashboard-1">
                <form action="salvar-mensalidade.php" method="POST" class="form-login">

                    <div class="campo">
                        <label for="aluno_id">Aluno</label>
                        <select name="aluno_id" id="aluno_id" required class="input-padrao">
                            <option value="">Selecione</option>
                            <?php while ($aluno = $alunos->fetch_assoc()) { ?>
                                <option value="<?php echo (int)$aluno["id"]; ?>">
                                    <?php echo htmlspecialchars($aluno["nome"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="valor">Valor</label>
                        <input type="number" step="0.01" name="valor" id="valor" required>
                    </div>

                    <div class="campo">
                        <label for="vencimento">Vencimento</label>
                        <input type="date" name="vencimento" id="vencimento" required>
                    </div>

                    <button type="submit" class="btn-login">Salvar mensalidade</button>
                </form>
            </div>
        </section>
    </main>
</div>

</body>
</html>