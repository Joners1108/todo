<?php
/**
 * Configuração da ligação à base de dados
 * Gestor de Tarefas - To-Do App
 */

$db_host = 'localhost';
$db_name = 'todo_app';
$db_user = 'root';
$db_pass = '';

try {
    // Criar conexão PDO com charset utf8mb4 para suporte a caracteres especiais
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Resposta JSON em caso de erro de conexão
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Erro na conexão com a base de dados',
        'details' => $e->getMessage()
    ]);
    exit;
}

// Configurar headers CORS para permitir comunicação com o frontend
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Tratar pré-requisições CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
