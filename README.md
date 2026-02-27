# TaskManager - Fullstack Task Management Application

A modern, responsive task management application built with **Laravel** (Backend API) and **Vue.js** (Frontend SPA).

## Tech Stack

### Backend
- **PHP 8.3** + **Laravel 12**
- **Laravel Sanctum** (Token-based Authentication)
- **MySQL 8.4** (Database)
- **RESTful API** Architecture

### Frontend
- **Vue 3** (Composition API)
- **Vite** (Build Tool)
- **Vue Router** (Client-side Routing)
- **Pinia** (State Management)
- **Axios** (HTTP Client)
- **Tailwind CSS** (Utility-first CSS)

## Features

- **Authentication** - Register, Login, Logout with token-based auth (Sanctum)
- **Dashboard** - Visual statistics with task counts by status, recent tasks overview
- **Task Management** - Full CRUD (Create, Read, Update, Delete) operations
- **Task Filtering** - Filter by status, priority, and search by title
- **Quick Actions** - One-click status change (Start / Complete)
- **Task Assignment** - Assign tasks to other team members
- **Priority Levels** - Low, Medium, High with visual indicators
- **Deadlines** - Set and track task deadlines
- **User Profile** - View and update profile information
- **Responsive Design** - Works on desktop, tablet, and mobile

## Prerequisites

- PHP >= 8.1
- Composer
- Node.js >= 16
- MySQL
- Git

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/task-manager.git
cd task-manager
```

### 2. Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_DATABASE=task_manager
# DB_USERNAME=root
# DB_PASSWORD=

# Create database
mysql -u root -e "CREATE DATABASE task_manager"

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

### 3. Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Start dev server
npm run dev
```

### 4. Access the Application

- **Frontend:** http://localhost:5173
- **Backend API:** http://localhost:8000/api

## API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register new user |
| POST | `/api/login` | Login |
| POST | `/api/logout` | Logout |
| GET | `/api/profile` | Get profile |
| PUT | `/api/profile` | Update profile |

### Tasks
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/dashboard` | Dashboard statistics |
| GET | `/api/tasks` | List tasks (with filters) |
| POST | `/api/tasks` | Create task |
| GET | `/api/tasks/{id}` | Get task detail |
| PUT | `/api/tasks/{id}` | Update task |
| DELETE | `/api/tasks/{id}` | Delete task |
| GET | `/api/users` | List users (for assignment) |

### Query Parameters for GET /api/tasks
- `status` - Filter by status: `pending`, `in_progress`, `completed`
- `priority` - Filter by priority: `low`, `medium`, `high`
- `search` - Search by task title

## Project Structure

```
task-manager/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   └── TaskController.php
│   │   └── Models/
│   │       ├── User.php
│   │       └── Task.php
│   ├── database/migrations/
│   ├── routes/api.php
│   └── ...
├── frontend/                # Vue.js SPA
│   ├── src/
│   │   ├── api/axios.js
│   │   ├── components/
│   │   │   ├── Navbar.vue
│   │   │   └── TaskModal.vue
│   │   ├── router/index.js
│   │   ├── stores/
│   │   │   ├── auth.js
│   │   │   └── task.js
│   │   ├── views/
│   │   │   ├── Login.vue
│   │   │   ├── Register.vue
│   │   │   ├── Dashboard.vue
│   │   │   ├── Tasks.vue
│   │   │   └── Profile.vue
│   │   ├── App.vue
│   │   └── main.js
│   └── ...
└── README.md
```

## Database Schema

### Users
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar | User's name |
| email | varchar | User's email (unique) |
| password | varchar | Hashed password |
| timestamps | - | created_at, updated_at |

### Tasks
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Task creator (FK → users) |
| assigned_to | bigint | Assigned user (FK → users, nullable) |
| title | varchar | Task title |
| description | text | Task description (nullable) |
| status | enum | pending, in_progress, completed |
| priority | enum | low, medium, high |
| deadline | date | Task deadline (nullable) |
| timestamps | - | created_at, updated_at |

## License

This project is open-source and available under the [MIT License](LICENSE).
