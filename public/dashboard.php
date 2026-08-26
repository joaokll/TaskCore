<?php

session_start();

if (!isset($_SESSION[''])) {
    $_SESSION[''] = 1;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">
    <title>TaskCore</title>
    <link rel="stylesheet" href="dashboard.css">    
</head>
<body>
<header>
    <h1>Task Manager</h1>
    <button
        class="btn-primary"
        onclick="criarBloco()">
        Novo bloco
    </button>
</header>
    <main id="blocos"></main>
    <button
        id="salvar"
        class="btn-success"
        onclick="salvarBanco()">
        Salvar
    </button>
    <script src="assets/app.js"></script>
</body>
</html>
