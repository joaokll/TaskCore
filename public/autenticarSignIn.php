<?php

require_once 'conexao.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    $senha = $_POST['senha'] ?? '';

    if ($email && !empty($senha)) {
        try {
            $stmt = $p->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id']   = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                header("");
                exit;
            }
        } catch (PDOException $e) {
            die("Erro na autenticação: " . $e->getMessage());
        }
    }
}