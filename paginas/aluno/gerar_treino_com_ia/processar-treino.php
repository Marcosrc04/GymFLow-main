<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["id"])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: gerar-treino.php");
    exit();
}

$objetivo = $_POST["objetivo"];
$nivel = $_POST["nivel"];
$dias = $_POST["dias"];
$foco = $_POST["foco"];

$treino = "";

if ($objetivo == "hipertrofia" && $nivel == "iniciante") {
    $treino = "
    Dia 1: Peito + Tríceps<br>
    - Supino 3x10<br>
    - Tríceps corda 3x12<br><br>

    Dia 2: Costas + Bíceps<br>
    - Puxada 3x10<br>
    - Rosca direta 3x12
    ";
} elseif ($objetivo == "emagrecimento") {
    $treino = "
    Treino Full Body:<br>
    - Agachamento 3x15<br>
    - Corrida 20min<br>
    - Abdominal 3x20
    ";
} else {
    $treino = "Ainda não existe treino cadastrado para essa combinação.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Treino Gerado | GymFlow</title>
    <link rel="stylesheet" href="../../../arquivos/css/style.css">
</head>
<body>

    <h1>Seu treino gerado 💪</h1>

    <p><?php echo $treino; ?></p>

    <br>

    <a href="gerar-treino.php">Gerar outro treino</a>

</body>
</html>