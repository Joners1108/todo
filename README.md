# To-Do App — Gestor de Tarefas

Aplicação web de gestão de tarefas com frontend em React 18 e backend em PHP 8+ com API REST. Base de dados MySQL. Comunicação via JSON sobre HTTP com suporte a CORS.

## Especificações técnicas

| Item | Valor |
|------|-------|
| Frontend | React 18.2.0, React DOM, React Scripts 5.0.1 (Create React App) |
| Backend | PHP 8+, PDO (MySQL), API REST |
| Base de dados | MySQL / MariaDB |
| Comunicação | JSON, fetch API, CORS habilitado |
| Build tool | npm / react-scripts |
| Tabela BD | `tasks` (id, title, description, completed, created_at) |

## Estrutura do repositório

```
todo/
├── backend/
│   ├── .htaccess          # Regras de routing Apache
│   ├── config/
│   │   └── db.php         # Ligação PDO a MySQL + headers CORS
│   └── api/
│       └── tasks.php      # Endpoints REST de tarefas
├── frontend/
│   ├── package.json       # Dependências React (react, react-dom, react-scripts)
│   ├── package-lock.json
│   ├── public/
│   │   └── index.html
│   └── src/
│       ├── index.js       # Ponto de entrada React
│       ├── App.js         # Componente principal
│       ├── App.css        # Estilos da aplicação
│       └── components/
│           ├── TaskForm.js
│           ├── TaskList.js
│           └── TaskItem.js
├── todo_app.sql           # Script de criação da base de dados e tabela
└── README.md
```

## Endpoints da API (`backend/api/tasks.php`)

Base path: `/api/tasks`

| Método | Endpoint | Descrição | Body de request |
|--------|----------|-----------|-----------------|
| GET | `/api/tasks` | Listar todas as tarefas (ordenadas por `created_at` DESC) | — |
| GET | `/api/tasks/{id}` | Obter tarefa específica | — |
| POST | `/api/tasks` | Criar nova tarefa | `{"title": string, "description": string\|null}` |
| PUT | `/api/tasks/{id}` | Atualizar tarefa | `{"title": string, "description": string\|null, "completed": bool}` |
| PATCH | `/api/tasks/{id}/complete` | Marcar como concluída/pendente | `{"completed": bool}` |
| DELETE | `/api/tasks/{id}` | Apagar tarefa | — |

## Variáveis de ambiente

Crie `backend/.env` a partir de `backend/.env.example` (não versionado):

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `DB_HOST` | Host do MySQL | `localhost` |
| `DB_NAME` | Nome da base de dados | `todo_app` |
| `DB_USER` | Utilizador MySQL | `root` |
| `DB_PASS` | Password MySQL | vazio |

Exemplo:

```
DB_HOST=localhost
DB_NAME=todo_app
DB_USER=root
DB_PASS=
```

## Instalação

### 1. Base de dados

1. Inicie o MySQL (XAMPP/Laragon ou equivalente).
2. Crie a base de dados `todo_app` com collation `utf8mb4_unicode_ci`.
3. Importe `todo_app.sql` para criar a tabela `tasks`.

### 2. Backend (PHP)

1. Copie a pasta `backend` para um diretório servido por Apache com PHP 8+.
2. Crie `backend/.env` com as credenciais da base de dados.
3. Verifique se a extensão `pdo_mysql` está ativada no PHP.
4. O ficheiro `backend/.htaccess` trata o routing para `api/tasks.php`.

### 3. Frontend (React)

1. Na pasta `frontend`, execute:

```
npm install
npm start
```

2. A aplicação estará disponível em `http://localhost:3000`.

### 4. CORS

O backend envia `Access-Control-Allow-Origin: *` e suporta pré-voo `OPTIONS`. Ajuste a origem em `backend/config/db.php` se necessário para produção.

## Notas de segurança

- O ficheiro `.env` está no `.gitignore`; não insira credenciais reais no repositório.
- As consultas SQL utilizam prepared statements (PDO) para prevenir injeção SQL.
- Em produção, restrinja `Access-Control-Allow-Origin` ao domínio do frontend.

## Licença

© 2026 João Pereira — https://github.com/Joners1108

Este projeto está disponível para uso pessoal e académico. Para uso comercial ou redistribuição, contacte o autor através do GitHub.

O código é fornecido "tal como está", sem garantias de qualquer tipo.
