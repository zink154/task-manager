# Tech Stack

## Backend

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.3 | Server-side language |
| Laravel | 12.x | Backend framework |
| Laravel Sanctum | 4.x | Token-based API authentication |
| Eloquent ORM | - | Database abstraction layer |

**Architecture:** RESTful API (stateless, JSON responses)

## Frontend

| Technology | Version | Purpose |
|------------|---------|---------|
| Vue.js | 3.x | Frontend framework (Composition API) |
| Vite | 7.x | Build tool & dev server |
| Vue Router | 4.x | Client-side routing with auth guards |
| Pinia | 3.x | State management |
| Axios | 1.x | HTTP client with interceptors |
| Tailwind CSS | 4.x | Utility-first CSS framework |

## Database

| Technology | Version | Purpose |
|------------|---------|---------|
| MySQL | 8.4 | Relational database |

**Schema:** Users (auth) → Tasks (CRUD with foreign keys)

## Deployment

| Component | Platform | URL |
|-----------|----------|-----|
| Frontend (Vue SPA) | Vercel | TBD |
| Backend (Laravel API) | Railway | TBD |
| Database (MySQL) | Railway | Internal |

## Development Tools

| Tool | Purpose |
|------|---------|
| Git + GitHub | Version control & repository hosting |
| Postman | API testing & documentation |
| VS Code | Code editor |
| Laragon | Local development environment (PHP, MySQL, Apache) |

## Features

### Authentication
- User registration with validation
- Login / Logout with Sanctum token
- Protected routes (frontend & backend)

### Task Management
- Full CRUD operations (Create, Read, Update, Delete)
- Status workflow: `Pending` → `In Progress` → `Completed`
- Priority levels: `Low` | `Medium` | `High`
- Deadline tracking
- Task assignment to team members
- Quick status change actions

### Dashboard
- Task statistics by status
- Recent tasks overview
- Visual progress indicators

### User Profile
- View and update profile information

### UI/UX
- Fully responsive design (mobile, tablet, desktop)
- Modern UI with gradient cards, icons, and animations
- Filter and search functionality
- Loading states and error handling
