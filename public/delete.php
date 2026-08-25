<?php
session_start();
$jsonFile = __DIR__ . '/../config/db.json';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

if (file_exists($jsonFile)) {
    $dadosSistema = json_decode(file_get_contents($jsonFile), true);
    
    // Filtra removendo o item que contém a ID passada via URL
    $dadosSistema['tarefas'] = array_filter($dadosSistema['tarefas'], function($t) {
        return $t['id'] !== $_GET['id'];
    });
    
    // Reindexa as chaves numéricas do array e salva de volta
    $dadosSistema['tarefas'] = array_values($dadosSistema['tarefas']);
    file_put_contents($jsonFile, json_encode($dadosSistema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

header("Location: dashboard.php");
exit;
