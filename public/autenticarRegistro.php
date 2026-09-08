<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carregando...</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #171c22;
            font-family: Arial, sans-serif;
            color: #263242;
        }

        .box {
            background-color: #161b22; 
            border: 1px solid #30363d;   
            border-radius: 6px;
            padding: 20px;
            width: 300px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .loader {
            border: 8px solid #171c22; 
            border-top: 8px solid #194372; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        .texto {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #263242;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="loader"></div>
        <div class="texto">Configurando o sistema...</div>
    
        <script>
            const tempoEspera = 3000; 
            const paginaDestino = "dashboard.php"; 

            setTimeout(function() {
                window.location.href = paginaDestino;
            }, tempoEspera);
        </script>
    </div>
</body>
</html>

<?php
require_once __DIR__ . '/../config/conexao.php';

$nome = $_POST['nome'];
$email = $_POST['email'];
$password = $_POST['password'];

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);

    if ($stmt->rowCount() == 0) {
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt_insert = $pdo->prepare($sql);
        $stmt_insert->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':senha' => $passwordHash
        ]);
        echo "Registro Concluido.";
        echo "E-mail: {$email} Senha: {$password}";
    } else {
        $sql = "UPDATE usuarios SET senha = :senha WHERE email = :email";
        $stmt_update = $pdo->prepare($sql);
        $stmt_update->execute([':senha' => $passwordHash, ':email' => $email]);
        echo "<h2> Senha redefinida</h2>";
    }
} catch (PDOException $e) {
    echo "Erro ao cadastrar usuário: " . $e->getMessage();
}
?>
