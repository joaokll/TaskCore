<?php

$host = 'localhost';
$dbname = 'taskManager';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

try {
    $dsnSemBanco = "mysql:host=$host;charset=$charset";
    $pdo = new PDO($dsnSemBanco, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $pdo->query("SHOW DATABASES LIKE '$dbname'");
    $bancoExiste = $query->rowCount() > 0;

    if (!$bancoExiste) {
        $caminhoSql = __DIR__ . '/banco.sql';
        
        if (file_exists($caminhoSql)) {
            $sql = file_get_contents($caminhoSql);
            $pdo->exec($sql);
        } else {
            die("Erro: O arquivo 'banco.sql' não foi encontrado na pasta do projeto.");
        }
    }
    
    $pdo->exec("USE `$dbname`");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
