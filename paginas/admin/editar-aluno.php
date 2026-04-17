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

$mensagem = "";
$tipo_mensagem = "";

$id = intval($_GET["id"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $nova_senha = $_POST["nova_senha"] ?? "";
    $confirmar_nova_senha = $_POST["confirmar_nova_senha"] ?? "";

    if (!empty($nova_senha)) {
        if ($nova_senha != $confirmar_nova_senha) {
            $mensagem = "As senhas não coincidem!";
            $tipo_mensagem = "erro";
        } else {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

            $sql_update = "UPDATE usuarios 
                           SET nome = '$nome', email = '$email', senha = '$senha_hash' 
                           WHERE id = $id AND tipo = 'aluno'";

            $conexao->query($sql_update);

            header("Location: dashboard-admin.php");
            exit();
        }
    } else {
        $sql_update = "UPDATE usuarios 
                       SET nome = '$nome', email = '$email' 
                       WHERE id = $id AND tipo = 'aluno'";

        $conexao->query($sql_update);

        header("Location: dashboard-admin.php");
        exit();
    }
}

$sql = "SELECT * FROM usuarios WHERE id = $id AND tipo = 'aluno'";
$resultado = $conexao->query($sql);
$aluno = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno | GymFlow</title>
    <link rel="stylesheet" href="../../arquivos/css/style.css">
    <link rel="shortcut icon" href="../../arquivos/imagem/GF.ico" type="image/x-icon">
</head>
<body>
    <div class="container-login">
        <div class="card-login">

            <h1>Editar Aluno</h1>
            <p class="subtitulo-editar-aluno">Atualize as informações do aluno abaixo:</p>

            <?php if (!empty($mensagem)) { ?>
                <div class="mensagem <?php echo $tipo_mensagem; ?>">
                    <?php echo $mensagem; ?>
                </div>
            <?php } ?>

            <form action="" method="post">
                <div class="campo">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" value="<?php echo $aluno["nome"]; ?>" required>
                </div>

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?php echo $aluno["email"]; ?>" required>
                </div>

                <div class="campo">
                    <label for="nova_senha">Nova Senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite uma nova senha (opcional)">
                </div>

                <div class="campo">
                    <label for="confirmar_nova_senha">Confirmar Nova Senha</label>
                    <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" placeholder="Confirme a nova senha (opcional)">
                </div>

                <p class="subtitulo-editar-aluno">Deixe os campos de senha em branco para manter a senha atual.</p>

                <button type="submit" class="btn-login">Salvar Alterações</button>
            </form>
        </div>
    </div>
</body>
</html>