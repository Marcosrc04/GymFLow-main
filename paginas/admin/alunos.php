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

$sql_alunos = "SELECT id, nome, email, tipo 
               FROM usuarios
               WHERE tipo = 'aluno'
               ORDER BY nome ASC";

$resultado_alunos = $conexao->query($sql_alunos);

if (!$resultado_alunos) {
    die("Erro na consulta: " . $conexao->error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos | GymFlow</title>
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
            <h1>Alunos</h1>
            <p>Gerencie os alunos cadastrados no sistema</p>
        </header>

        <section class="blocos-dashboard-admin">
            <div class="bloco-dashboard-1">
                <a href="../../cadastro.php" class="btn-novo">+ Novo aluno</a>

                <div class="tabela-scroll">
                    <table class="tabela-dashboard">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Tipo</th>
                                <th>Ação</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($resultado_alunos->num_rows > 0) { ?>
                                <?php while ($aluno = $resultado_alunos->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($aluno["nome"]); ?></td>
                                        <td class="coluna-email"><?php echo htmlspecialchars($aluno["email"]); ?></td>
                                        <td><?php echo ucfirst(htmlspecialchars($aluno["tipo"])); ?></td>
                                        <td class="coluna-acoes">
                                            <a href="perfil-aluno.php?id=<?php echo (int)$aluno["id"]; ?>">Ver</a> |
                                            <a href="editar-aluno.php?id=<?php echo (int)$aluno["id"]; ?>">Editar</a> |
                                            <a href="excluir-aluno.php?id=<?php echo (int)$aluno["id"]; ?>"
                                               onclick="return confirm('Tem certeza que deseja excluir?')">
                                                Excluir
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="4">Nenhum aluno encontrado.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

</body>
</html>