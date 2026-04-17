<?php
include("includes/conexao.php");

$nome = $_POST["nome"] ?? "";
$email = $_POST["email"] ?? "";
$senha = $_POST["senha"] ?? "";
$tipo = $_POST["tipo"] ?? "aluno";

if (empty($nome) || empty($email) || empty($senha)) {
    die("Preencha todos os campos obrigatórios.");
}

$foto_nome_final = null;

if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === 0) {

    $pasta_destino = "arquivos/imagem/perfis/";

    $nome_original = $_FILES["foto"]["name"];
    $tmp_nome = $_FILES["foto"]["tmp_name"];
    $tamanho = $_FILES["foto"]["size"];

    $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));

    $extensoes_permitidas = ["jpg", "jpeg", "png", "webp"];

    if (in_array($extensao, $extensoes_permitidas)) {

        if ($tamanho <= 2 * 1024 * 1024) { // 2MB

            $foto_nome_final = uniqid("perfil_", true) . "." . $extensao;
            $caminho_final = $pasta_destino . $foto_nome_final;

            if (!move_uploaded_file($tmp_nome, $caminho_final)) {
                die("Erro ao salvar a imagem.");
            }

        } else {
            die("A imagem deve ter no máximo 2MB.");
        }

    } else {
        die("Formato inválido. Use JPG, PNG ou WEBP.");
    }
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha, tipo, foto)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("sssss", $nome, $email, $senha_hash, $tipo, $foto_nome_final);

if ($stmt->execute()) {
    header("Location: login.php");
    exit();
} else {
    die("Erro ao cadastrar: " . $conexao->error);
}
?>