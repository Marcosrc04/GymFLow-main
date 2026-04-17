<?php
session_start();
include("includes/conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = $conexao->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario["senha"])) {
            $_SESSION["id"] = $usuario["id"];
            $_SESSION["nome"] = $usuario["nome"];
            $_SESSION["tipo"] = $usuario["tipo"];
            $_SESSION["foto"] = $usuario["foto"];

            if ($usuario["tipo"] == "aluno") {
                header("Location: paginas/aluno/dashboard-aluno.php");
                exit();
            } elseif ($usuario["tipo"] == "admin") {
                header("Location: paginas/admin/dashboard-admin.php");
                exit();
            }
        } else {
            echo "Senha incorreta!";
        }
    } else {
        echo "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GymFlow</title>

    <link rel="shortcut icon" href="arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="arquivos/css/style.css">
</head>

<body>

    <div class="container-login">
        <div class="card-login">

            <img src="arquivos/imagem/1312fedc-983d-42d0-80df-ada4981193f2.png" alt="Logo GymFlow" class="logo">

            <h1>Bem-vindo de volta</h1>
            <p class="subtitulo">Acesse sua conta e continue sua evolução</p>

            <form class="form-login" action="" method="post">

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu e-mail">
                </div>

                <div class="campo">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha">
                </div>

                <button type="submit" class="btn-login">Entrar</button>

                <div class="links-login">
                    <a href="#">Esqueci minha senha</a>
                    <a href="cadastro.php">Criar conta</a>
                </div>

            </form>

        </div>
    </div>

</body>

</html>