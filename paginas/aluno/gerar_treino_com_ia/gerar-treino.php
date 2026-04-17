<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <form action="processar-treino.php" method="post">
        <label>Objetivo:</label>
        <select name="objetivo">
            <option value="emagrecimento">emagrecimento</option>
            <option value="hipertrofia">hipertrofia</option>
        </select>

<br><br>

    <label>Nível</label>
    <select name="nivel:">
        <option value="iniciante">iniciante</option>
        <option value="intermediario">intermediario</option>
    </select>

    
    <br><br>
    
    <label>Dias por semana:</label>
    <input type="number" name="dias" min="1" max="7">
    
    <br><br>
    
    <label>Foco:</label>
    <input type="text" name="foco" placeholder="ex: peito, perna...">
    
    <br><br>
    
    <button type="submit">Gerar treino</button>
</form>


</body>
</html>