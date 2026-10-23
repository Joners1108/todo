# Gestor de Tarefas (To-Do App)

Aplicação web para gestão de tarefas com Frontend em React e Backend em PHP.

## 🚀 Tecnologias

- **Frontend:** React 18 (componentes funcionais e Hooks)
- **Backend:** PHP 8+ com API REST
- **Base de Dados:** MySQL
- **Comunicação:** JSON via REST API

## 📁 Estrutura do Projeto

```
todo/
├── backend/
│   ├── config/
│   │   └── db.php          # Configuração da ligação à BD
│   ├── api/
│   │   └── tasks.php       # Endpoints da API REST
│   ├── .htaccess           # Regras de routing
│   └── todo_app.sql        # Script de criação da BD
│
└── frontend/
    ├── public/
    │   └── index.html
    ├── src/
    │   ├── components/
    │   │   ├── TaskForm.js    # Formulário de tarefas
    │   │   ├── TaskList.js    # Lista de tarefas
    │   │   └── TaskItem.js     # Item individual
    │   ├── App.js              # Componente principal
    │   ├── App.css             # Estilos
    │   └── index.js           # Entry point
    └── package.json
```

---

## 🏃 Como Iniciar (Sem Mexer em Código)

### Passo 1: Iniciar o XAMPP (Base de Dados + Apache)

1. Abra o **XAMPP Control Panel**
2. Inicie os módulos:
   - **Apache** (servidor web)
   - **MySQL** (base de dados)
3. Clique em **Admin** ao lado do MySQL para abrir o phpMyAdmin

### Passo 2: Criar a Base de Dados

1. No phpMyAdmin (http://localhost/phpmyadmin):
   - Clique em **Novo** no menu lateral
   - Nome da base de dados: `todo_app`
   - Selecione **utf8mb4_unicode_ci** como collation
   - Clique em **Criar**

2. Clique na base de dados `todo_app` criada
3. Clique no separador **Importar**
4. Clique em **Escolher ficheiro** e selecione: `todo_app.sql`
5. Role até ao fundo e clique em **Executar**

> ✅ A tabela `tasks` será criada automaticamente!

### Passo 3: Iniciar o Frontend (React)

Abra um terminal na pasta do projeto e execute:

```bash
cd frontend
npm install
npm start
```

> ✅ A aplicação abrirá automaticamente em http://localhost:3000

### Passo 4: Verificar se Tudo Funciona

1. Certifique-se que o **Apache** e **MySQL** estão a correr no XAMPP
2. Abra http://localhost:3000 no navegador
3. Crie uma tarefa de teste - se aparecer na lista, está tudo a funcionar!

---

## 📡 Endpoints da API

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/tasks` | Listar todas as tarefas |
| POST | `/api/tasks` | Criar nova tarefa |
| GET | `/api/tasks/{id}` | Obter tarefa específica |
| PUT | `/api/tasks/{id}` | Atualizar tarefa |
| PATCH | `/api/tasks/{id}/complete` | Marcar como concluída |
| DELETE | `/api/tasks/{id}` | Apagar tarefa |

### Exemplos de Requests/Responses

**GET /api/tasks**
```json
[
  {
    "id": 1,
    "title": "Aprender React",
    "description": "Estudar hooks e componentes",
    "created_at": "2024-01-01 12:00:00",
    "completed": 1
  }
]
```

**POST /api/tasks**
```json
// Request
{
  "title": "Nova tarefa",
  "description": "Descrição opcional"
}

// Response
{
  "id": 2,
  "title": "Nova tarefa",
  "description": "Descrição opcional",
  "created_at": "2024-01-02 14:30:00",
  "completed": 0
}
```

**PUT /api/tasks/1**
```json
// Request
{
  "title": "Tarefa atualizada",
  "description": "Nova descrição",
  "completed": true
}

// Response
{
  "id": 1,
  "title": "Tarefa atualizada",
  "description": "Nova descrição",
  "created_at": "2024-01-01 12:00:00",
  "completed": 1
}
```

## 🎨 Funcionalidades

- ✅ Criar tarefa (título obrigatório, descrição opcional)
- ✅ Listar todas as tarefas
- ✅ Editar tarefa (título, descrição, estado)
- ✅ Marcar tarefa como concluída/pendente
- ✅ Apagar tarefa
- ✅ Feedback visual de tarefas concluídas (texto riscado)
- ✅ Mensagens de erro/sucesso
- ✅ Design responsivo

## 📝 Notas

- O frontend comunica diretamente com a API via fetch
- CORS está configurado no backend para permitir pedidos do React (localhost:3000)
- As datas são armazenadas no formato DATETIME do MySQL

## 🔨 Possíveis Problemas

1. **Erro CORS:** Verifique se o header `Access-Control-Allow-Origin` está correto no `db.php`
2. **Erro de conexão:** Verifique as credenciais MySQL em `config/db.php`
3. **API não encontrada:** Verifique se o .htaccess está a funcionar corretamente

---

Desenvolvido com ❤️ para o desafio To-Do App
