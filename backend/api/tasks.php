<?php
/**
 * API REST - Gestor de Tarefas (To-Do App)
 * 
 * Endpoints disponíveis:
 * - GET    /api/tasks          - Listar todas as tarefas
 * - POST   /api/tasks          - Criar nova tarefa
 * - GET    /api/tasks/{id}     - Obter tarefa específica
 * - PUT    /api/tasks/{id}     - Atualizar tarefa
 * - PATCH  /api/tasks/{id}/complete - Marcar como concluída
 * - DELETE /api/tasks/{id}     - Apagar tarefa
 * 
 * Exemplos de requests e responses em JSON:
 * 
 * GET /api/tasks
 * Response: [
 *   {"id": 1, "title": "Tarefa 1", "description": "Descrição", "created_at": "2024-01-01 12:00:00", "completed": false},
 *   {"id": 2, "title": "Tarefa 2", "description": null, "created_at": "2024-01-02 12:00:00", "completed": true}
 * ]
 * 
 * POST /api/tasks
 * Request: {"title": "Nova tarefa", "description": "Descrição opcional"}
 * Response: {"id": 3, "title": "Nova tarefa", "description": "Descrição opcional", "created_at": "2024-01-03 12:00:00", "completed": false}
 * 
 * PUT /api/tasks/1
 * Request: {"title": "Tarefa atualizada", "description": "Nova descrição", "completed": true}
 * Response: {"id": 1, "title": "Tarefa atualizada", "description": "Nova descrição", "created_at": "2024-01-01 12:00:00", "completed": true}
 * 
 * PATCH /api/tasks/1/complete
 * Request: {"completed": true}
 * Response: {"id": 1, "title": "Tarefa 1", "description": "...", "created_at": "...", "completed": true}
 * 
 * DELETE /api/tasks/1
 * Response: {"message": "Tarefa apagada com sucesso"}
 */

// Incluir configuração da base de dados
require_once __DIR__ . '/../config/db.php';

// Obter o método HTTP e a URI da requisição
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Extrair o ID da tarefa da URL se presente
$id = null;
if (preg_match('/\/tasks\/(\d+)/', $path, $matches)) {
    $id = (int) $matches[1];
}

// Verificar se é uma requisição para marcar como concluída (PATCH /tasks/{id}/complete)
$is_complete_route = false;
if ($method === 'PATCH' && preg_match('/\/api\/tasks\/\d+\/complete/', $path)) {
    $is_complete_route = true;
}

// Router simples para os endpoints
switch ($method) {
    case 'GET':
        if ($id) {
            getTask($pdo, $id);
        } else {
            getAllTasks($pdo);
        }
        break;
        
    case 'POST':
        createTask($pdo);
        break;
        
    case 'PUT':
        // PUT pode enviar ID no body ou na URL
        $data = json_decode(file_get_contents('php://input'), true);
        $bodyId = isset($data['id']) ? (int) $data['id'] : null;
        if ($id || $bodyId) {
            updateTask($pdo, $id ?: $bodyId);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID da tarefa é obrigatório']);
        }
        break;
        
    case 'PATCH':
        // PATCH pode enviar ID no body
        $data = json_decode(file_get_contents('php://input'), true);
        $bodyId = isset($data['id']) ? (int) $data['id'] : null;
        if ($id && $is_complete_route) {
            completeTask($pdo, $id);
        } elseif ($id) {
            updateTask($pdo, $id);
        } elseif ($bodyId) {
            // Se tem ID no body, verificar se é para completar ou atualizar
            if (isset($data['completed'])) {
                completeTask($pdo, $bodyId);
            } else {
                updateTask($pdo, $bodyId);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID da tarefa é obrigatório']);
        }
        break;
        
    case 'DELETE':
        // DELETE pode enviar ID no body ou na URL
        $data = json_decode(file_get_contents('php://input'), true);
        $bodyId = isset($data['id']) ? (int) $data['id'] : null;
        if ($id || $bodyId) {
            deleteTask($pdo, $id ?: $bodyId);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID da tarefa é obrigatório']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}

/**
 * GET /api/tasks - Listar todas as tarefas
 */
function getAllTasks($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
        $tasks = $stmt->fetchAll();
        
        echo json_encode($tasks);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao listar tarefas', 'details' => $e->getMessage()]);
    }
}

/**
 * GET /api/tasks/{id} - Obter tarefa específica
 */
function getTask($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $task = $stmt->fetch();
        
        if ($task) {
            echo json_encode($task);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Tarefa não encontrada']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao obter tarefa', 'details' => $e->getMessage()]);
    }
}

/**
 * POST /api/tasks - Criar nova tarefa
 * Request: {"title": "string", "description": "string|null"}
 */
function createTask($pdo) {
    // Ler dados do corpo da requisição
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar título (obrigatório)
    if (empty($data['title']) || trim($data['title']) === '') {
        http_response_code(400);
        echo json_encode(['error' => 'O título é obrigatório']);
        return;
    }
    
    $title = trim($data['title']);
    $description = isset($data['description']) ? trim($data['description']) : null;
    $completed = isset($data['completed']) ? (bool) $data['completed'] : false;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, completed, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$title, $description, $completed]);
        
        $id = (int) $pdo->lastInsertId();
        
        // Obter a tarefa criada
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $task = $stmt->fetch();
        
        http_response_code(201);
        echo json_encode($task);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao criar tarefa', 'details' => $e->getMessage()]);
    }
}

/**
 * PUT /api/tasks/{id} - Atualizar tarefa
 * Request: {"title": "string", "description": "string|null", "completed": bool}
 */
function updateTask($pdo, $id) {
    // Verificar se a tarefa existe
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch();
    
    if (!$task) {
        http_response_code(404);
        echo json_encode(['error' => 'Tarefa não encontrada']);
        return;
    }
    
    // Ler dados do corpo da requisição
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar título (obrigatório)
    if (isset($data['title']) && (empty($data['title']) || trim($data['title']) === '')) {
        http_response_code(400);
        echo json_encode(['error' => 'O título não pode estar vazio']);
        return;
    }
    
    // Usar valores existentes se não forem fornecidos
    $title = isset($data['title']) ? trim($data['title']) : $task['title'];
    $description = isset($data['description']) ? ($data['description'] === '' ? null : trim($data['description'])) : $task['description'];
    $completed = isset($data['completed']) ? (bool) $data['completed'] : (bool) $task['completed'];
    
    try {
        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, completed = ? WHERE id = ?");
        $stmt->execute([$title, $description, $completed, $id]);
        
        // Obter a tarefa atualizada
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $updated_task = $stmt->fetch();
        
        echo json_encode($updated_task);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar tarefa', 'details' => $e->getMessage()]);
    }
}

/**
 * PATCH /api/tasks/{id}/complete - Marcar tarefa como concluída
 * Request: {"completed": bool}
 */
function completeTask($pdo, $id) {
    // Verificar se a tarefa existe
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch();
    
    if (!$task) {
        http_response_code(404);
        echo json_encode(['error' => 'Tarefa não encontrada']);
        return;
    }
    
    // Ler dados do corpo da requisição
    $data = json_decode(file_get_contents('php://input'), true);
    $completed = isset($data['completed']) ? (bool) $data['completed'] : true;
    
    try {
        $stmt = $pdo->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
        $stmt->execute([$completed, $id]);
        
        // Obter a tarefa atualizada
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $updated_task = $stmt->fetch();
        
        echo json_encode($updated_task);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar tarefa', 'details' => $e->getMessage()]);
    }
}

/**
 * DELETE /api/tasks/{id} - Apagar tarefa
 */
function deleteTask($pdo, $id) {
    // Verificar se a tarefa existe
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch();
    
    if (!$task) {
        http_response_code(404);
        echo json_encode(['error' => 'Tarefa não encontrada']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['message' => 'Tarefa apagada com sucesso']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao apagar tarefa', 'details' => $e->getMessage()]);
    }
}
