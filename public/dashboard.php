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

    <title>Task Manager</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f5f7;
            color: #222;
        }

        header {
            background: #222;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        button {
            border: 0;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        #blocos {
            padding: 30px;
            display: grid;
            gap: 20px;
        }

        .bloco {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }

        .bloco-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .bloco-header input {
            font-size: 20px;
            font-weight: bold;
            border: 0;
            outline: none;
            width: 100%;
        }

        .tarefa {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
        }

        .tarefa input[type="text"],
        .tarefa textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .tarefa textarea {
            min-height: 80px;
            resize: vertical;
        }

        .tarefa-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .acoes {
            display: flex;
            gap: 8px;
        }

        #salvar {
            position: fixed;
            right: 25px;
            bottom: 25px;
            font-size: 16px;
            padding: 15px 25px;
        }

    </style>

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
