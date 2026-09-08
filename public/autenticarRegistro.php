<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carregando...</title>
</head>
<body>
    
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
    echo '<br></br><a href="login.php">Faça Login</a>';
} catch (PDOException $e) {
    echo "Erro ao cadastrar usuário: " . $e->getMessage();
}
?>