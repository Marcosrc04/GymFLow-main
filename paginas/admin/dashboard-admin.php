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

$sql_total_pagas = "SELECT COUNT(*) as total FROM pagamentos WHERE status = 'pago'";
$result_total_pagas = $conexao->query($sql_total_pagas);
$total_pagas = $result_total_pagas->fetch_assoc()["total"];

$sql_total_atrasados = "SELECT COUNT(*) as total FROM pagamentos WHERE status = 'atrasado'";
$result_total_atrasados = $conexao->query($sql_total_atrasados);
$total_atrasados = $result_total_atrasados->fetch_assoc()["total"];


$sql_total_pendentes = "SELECT COUNT(*) as total FROM pagamentos WHERE status = 'pendente'";
$result_total_pendentes = $conexao->query($sql_total_pendentes);
$total_pendentes = $result_total_pendentes->fetch_assoc()["total"];

$sql_total_alunos = "SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'aluno'";
$result_total = $conexao->query($sql_total_alunos);
$total_alunos = $result_total->fetch_assoc()["total"];

$sql_tabela_dashboard = "SELECT 
                            u.id,
                            u.nome,
                            u.email,
                            u.tipo,
                            p.status,
                            p.valor,
                            p.vencimento
                         FROM usuarios u
                         LEFT JOIN pagamentos p 
                            ON p.id = (
                                SELECT MAX(id)
                                FROM pagamentos
                                WHERE aluno_id = u.id
                            )
                         WHERE u.tipo = 'aluno'
                         ORDER BY p.vencimento ASC, u.nome ASC";

$resultado_tabela_dashboard = $conexao->query($sql_tabela_dashboard);

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
    <title>Dashboard do Administrador | GymFlow</title>

    <link rel="shortcut icon" href="../../arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../arquivos/css/style.css">
</head>

<body>

    <div class="layout-dashboard">

        <aside class="sidebar">
            <div class="topo-sidebar">
                <img src="../../arquivos/imagem/1312fedc-983d-42d0-80df-ada4981193f2.png" alt="Logo GymFlow"
                    class="logo-sidebar">
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
                <div class="header-esquerda">

                    <h1 class="titulo-destaque">
                        <?php echo $saudacao; ?>,
                        <span class="nome-usuario">
                            <?php echo htmlspecialchars($_SESSION["nome"]); ?>
                        </span> 👋
                    </h1>
                    <p class="subtitulo-dashboard">
                        Vamos cuidar dos seus alunos, pagamentos e da gestão da academia 💪
                    </p>
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
                    <h3>Total de Alunos</h3>
                    <p><?php echo $total_alunos; ?></p>
                </div>

                <div class="card-resumo">
                    <h3>Mensalidades Pagas</h3>
                    <p><?php echo $total_pagas; ?></p>
                </div>

                <div class="card-resumo">
                    <h3>Atrasados</h3>
                    <p><?php echo $total_atrasados; ?></p>
                </div>

                <div class="card-resumo">
                    <h3>Pendentes</h3>
                    <p><?php echo $total_pendentes; ?></p>
                </div>
            </section>

            <section class="blocos-dashboard-admin">
                <div class="bloco-dashboard-1">
                    <h2>Alunos e Mensalidades</h2>

                    <div class="tabela-scroll">
                        <table class="tabela-dashboard">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                    <th>Ação</th>
                                    <th>Mais</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($resultado_tabela_dashboard && $resultado_tabela_dashboard->num_rows > 0) { ?>
                                    <?php while ($linha = $resultado_tabela_dashboard->fetch_assoc()) { ?>
                                        <?php
                                        $status = !empty($linha["status"]) ? $linha["status"] : "sem-status";

                                        if ($status != "pago" && !empty($linha["vencimento"]) && $linha["vencimento"] < date("Y-m-d")) {
                                            $status = "atrasado";
                                        }

                                        $statusTexto = ($status != "sem-status") ? ucfirst($status) : "Sem registro";

                                        $valorFormatado = !empty($linha["valor"])
                                            ? "R$ " . number_format($linha["valor"], 2, ',', '.')
                                            : "-";

                                        $vencimentoFormatado = !empty($linha["vencimento"])
                                            ? date("d/m/Y", strtotime($linha["vencimento"]))
                                            : "-";

                                        $vencimentoFormatado = !empty($linha["vencimento"])
                                            ? date("d/m/Y", strtotime($linha["vencimento"]))
                                            : "-";
                                        ?>

                                        <tr>
                                            <td><?php echo htmlspecialchars($linha["nome"]); ?></td>

                                            <td class="coluna-email">
                                                <?php echo htmlspecialchars($linha["email"]); ?>
                                            </td>

                                            <td><?php echo ucfirst(htmlspecialchars($linha["tipo"])); ?></td>

                                            <td>
                                                <span class="status <?php echo htmlspecialchars($status); ?>">
                                                    <?php echo $statusTexto; ?>
                                                </span>
                                            </td>

                                            <td><?php echo $valorFormatado; ?></td>

                                            <td><?php echo $vencimentoFormatado; ?></td>

                                            <td class="coluna-acoes">
                                                <a href="editar-aluno.php?id=<?php echo $linha["id"]; ?>">Editar</a> |
                                                <a href="excluir-aluno.php?id=<?php echo $linha["id"]; ?>"
                                                    onclick="return confirm('Tem certeza que deseja excluir?')">
                                                    Excluir
                                                </a>
                                            </td>

                                            <td>
                                                <button type="button" class="btn-expandir" aria-label="Expandir detalhes">
                                                    ▼
                                                </button>
                                            </td>
                                        </tr>

                                        <tr class="linha-detalhes">
                                            <td colspan="8">
                                                <div class="detalhes-pagamentos">
                                                    <p><strong>Aluno:</strong> <?php echo htmlspecialchars($linha["nome"]); ?>
                                                    </p>
                                                    <p><strong>Status atual:</strong> <?php echo $statusTexto; ?></p>
                                                    <p><strong>Valor:</strong> <?php echo $valorFormatado; ?></p>
                                                    <p><strong>Vencimento:</strong> <?php echo $vencimentoFormatado; ?></p>
                                                    <p><strong>Observação:</strong> aqui depois vamos puxar o histórico real de
                                                        pagamentos desse aluno.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="8">Nenhum aluno encontrado.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </main>

    </div>

    <script>
        document.querySelectorAll(".btn-expandir").forEach(function (botao) {
            botao.addEventListener("click", function () {
                const linhaAtual = botao.closest("tr");
                const detalhes = linhaAtual.nextElementSibling;

                if (detalhes.style.display === "table-row") {
                    detalhes.style.display = "none";
                    botao.textContent = "▼";
                } else {
                    detalhes.style.display = "table-row";
                    botao.textContent = "▲";
                }
            });
        });
    </script>
</body>

</html>