<?php
include("includes/conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $confirmar_senha = $_POST["confirmar_senha"];

    if ($senha != $confirmar_senha) {
        echo "As senhas não coincidem!";
    } else {
        $verificar = "SELECT * FROM usuarios WHERE email = '$email'";
        $resultado_verificar = $conexao->query($verificar);

        if ($resultado_verificar->num_rows > 0) {
            echo "Este e-mail já está cadastrado!";
        } else {
            $foto_nome_final = null;

            if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === 0) {
                $pasta_destino = "arquivos/imagem/perfis/";

                $nome_original = $_FILES["foto"]["name"];
                $tmp_nome = $_FILES["foto"]["tmp_name"];
                $tamanho = $_FILES["foto"]["size"];

                $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
                $extensoes_permitidas = ["jpg", "jpeg", "png", "webp"];

                if (in_array($extensao, $extensoes_permitidas)) {
                    if ($tamanho <= 2 * 1024 * 1024) {
                        $foto_nome_final = uniqid("perfil_", true) . "." . $extensao;
                        $caminho_final = $pasta_destino . $foto_nome_final;

                        if (!move_uploaded_file($tmp_nome, $caminho_final)) {
                            die("Erro ao salvar a imagem.");
                        }
                    } else {
                        die("A imagem deve ter no máximo 2MB.");
                    }
                } else {
                    die("Formato inválido. Use JPG, JPEG, PNG ou WEBP.");
                }
            }

            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, email, senha, tipo)
        VALUES ('$nome', '$email', '$senha_hash', 'aluno')";

            if ($conexao->query($sql) === TRUE) {
                header("Location: login.php");
                exit();
            } else {
                echo "Erro ao cadastrar!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | GymFlow</title>

    <link rel="shortcut icon" href="arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="arquivos/css/style.css">
</head>


<body>

    <div class="container-login">
        <div class="card-login">

            <img src="arquivos/imagem/1312fedc-983d-42d0-80df-ada4981193f2.png" alt="Logo GymFlow" class="logo">

            <h1>Criar conta</h1>
            <p class="subtitulo">Cadastre-se para começar sua evolução no GymFlow</p>

            <form action="cadastro.php" class="form-login" method="post" enctype="multipart/form-data">

                <div class="campo">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required>
                </div>

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
                </div>

                <div class="campo">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Crie uma senha" required>
                </div>

                <div class="campo">
                    <label for="confirmar_senha">Confirmar senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha"
                        required>
                </div>

                <div class="campo">
                    <label for="foto">Foto de perfil</label>

                    <input type="file" name="foto" id="foto" accept="image/*" style="display: none;">

                    <label for="foto" class="btn-upload">📷 Escolher foto</label>

                    <span id="nome-arquivo">Nenhum arquivo escolhido</span>
                </div>

                <button type="submit" class="btn-login">Cadastrar</button>

                <div class="links-login">
                    <a href="login.php">Já tenho conta</a>
                </div>

            </form>

        </div>
    </div>

    <script>
        const inputFoto = document.getElementById("foto");
        const nomeArquivo = document.getElementById("nome-arquivo");

        inputFoto.addEventListener("change", function() {
            if (this.files.length > 0) {
                nomeArquivo.textContent = this.files[0].name;
            } else {
                nomeArquivo.textContent = "Nenhum arquivo escolhido";
            }
        });
    </script>
</body>

</html>