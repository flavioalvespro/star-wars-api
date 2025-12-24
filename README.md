# Star Wars Full Stack Application

A full-stack application consisting of a Laravel REST API backend and a React frontend that integrates with the [SWAPI (Star Wars API)](https://swapi.tech/) to provide search functionality for Star Wars characters and films, with built-in search analytics and statistics.

## 🚀 Quick Start

You can run this project in two ways:

### Option A: Full Stack (API + Frontend) - Recommended for Development

```bash
cd star-wars-api
docker-compose -f docker-compose-dev.yml up -d
```

**What this runs:**
- ✅ Laravel API (PHP-FPM + Nginx)
- ✅ React Frontend (Vite dev server)
- ✅ MySQL Database
- ✅ Redis (Cache & Queue)
- ✅ Queue Worker & Scheduler

**Access:**
- **Frontend**: http://localhost:5173
- **API**: http://localhost

**Stop:**
```bash
docker-compose -f docker-compose-dev.yml down
```

---

### Option B: API Only (Backend)

```bash
cd star-wars-api
docker-compose up -d
```

**What this runs:**
- ✅ Laravel API (PHP-FPM + Nginx)
- ✅ MySQL Database
- ✅ Redis (Cache & Queue)
- ✅ Queue Worker & Scheduler

**Access:**
- **API**: http://localhost

**Stop:**
```bash
docker-compose down
```

---

### 📋 Quick Reference

| Command | What it runs | Use when |
|---------|-------------|----------|
| `docker-compose up -d` | API only | Backend development, API testing |
| `docker-compose -f docker-compose-dev.yml up -d` | API + Frontend | Full-stack development |

> **💡 Tip**: The difference is the `-f docker-compose-dev.yml` flag. Without it, Docker uses `docker-compose.yml` (API only) by default.

## Features

- **People Search**: Search for Star Wars characters by name
- **Films Search**: Search for Star Wars films by title
- **Search Analytics**: Automatic tracking of all search queries
- **Statistics Dashboard**: Real-time statistics about search patterns, including:
  - Top 5 most searched queries with percentages
  - Average API response time
  - Most popular hours for searches
- **Automated Statistics Computation**: Background job runs every 5 minutes to compute fresh statistics
- **Queue System**: Redis-backed queue for background job processing
- **Caching**: Redis-based caching for improved performance
- **Comprehensive Testing**: Feature tests with 100% endpoint coverage

## Tech Stack

### Backend (API)
- **PHP 8.3**
- **Laravel 12**
- **MySQL 8.0** - Main database
- **Redis 7** - Caching, sessions, and queue management
- **Nginx** - Web server

### Frontend
- **React 19** - UI library
- **Vite 7** - Build tool and dev server
- **Axios** - HTTP client for API requests
- **React Router DOM 7** - Client-side routing

### DevOps
- **Docker & Docker Compose** - Containerized environment
- **Hot Module Replacement (HMR)** - Instant updates during development

## Architecture

The application follows a clean architecture pattern with:
- **Controllers** - Handle HTTP requests
- **Services** - Business logic layer
- **Repositories** - Data access layer
- **Resources** - API response formatting
- **Jobs** - Background processing tasks

## Prerequisites

Before running this project, make sure you have installed:

- [Docker](https://docs.docker.com/get-docker/) (version 20.10 or higher)
- [Docker Compose](https://docs.docker.com/compose/install/) (version 2.0 or higher)

## Installation & Setup

This project has two Docker Compose configurations:

| File | Command | What it runs | Purpose |
|------|---------|-------------|---------|
| `docker-compose.yml` | `docker-compose up -d` | **API only** | Backend development, API testing |
| `docker-compose-dev.yml` | `docker-compose -f docker-compose-dev.yml up -d` | **API + Frontend** | Full-stack development |

Choose the setup that matches your needs:

---

## 🔧 Option 1: Full Stack Development (API + Frontend)

This is the recommended setup for full-stack development with hot reload for both backend and frontend.

### 1. Clone Both Repositories

```bash
# Clone the API
git clone git@github.com:flavioalvespro/star-wars-api.git
cd ..

# Clone the Frontend (in the same parent directory)
git clone git@github.com:flavioalvespro/star-wars-frontend.git
```

**Important**: Both projects must be in the same parent directory:
```
dev/
├── star-wars-api/
└── star-wars-frontend/
```

### 2. Configure Environment Variables

#### API Configuration
```bash
cd star-wars-api
cp .env.example .env
```

Update the `.env` file as described in the [Environment Variables](#environment-variables-reference) section.

#### Frontend Configuration
The frontend is pre-configured to connect to the API via Docker network. No additional configuration needed!

### 3. Start the Full Stack

From the `star-wars-api` directory:

```bash
docker-compose -f docker-compose-dev.yml up -d
```

This single command will start:
- ✅ Laravel API (PHP-FPM + Nginx)
- ✅ MySQL Database
- ✅ Redis (Cache & Queue)
- ✅ Queue Worker
- ✅ Scheduler
- ✅ React Frontend (Vite Dev Server)

### 4. Initialize the Database

```bash
docker exec -it star-wars-api-app sh

# Inside the container:
php artisan key:generate
composer install
php artisan migrate
exit
```

### 5. Access the Applications

- **Frontend**: http://localhost:5173 (React app with hot reload)
- **API**: http://localhost (Laravel API)
- **Database**: localhost:3307 (MySQL)

### 6. Development Workflow

#### Frontend Development
The frontend has **hot reload** enabled. Simply edit files in `../star-wars-frontend/src/` and see changes instantly in the browser!

**Frontend Structure:**
```
star-wars-frontend/
├── src/
│   ├── components/      # Reusable React components
│   ├── pages/          # Page components (routes)
│   ├── services/       # API integration layer
│   │   ├── api.js           # Axios configuration
│   │   └── peopleService.js # People endpoint calls
│   ├── hooks/          # Custom React hooks
│   ├── utils/          # Utility functions
│   ├── assets/         # Images, fonts, etc
│   └── styles/         # Global styles
├── Dockerfile          # Frontend container config
└── .env                # Frontend environment variables
```

#### Backend Development
The backend also has hot reload via volume mounting. Edit PHP files and refresh to see changes!

#### Viewing Logs
```bash
# Frontend logs
docker logs -f star-wars-frontend

# API logs
docker logs -f star-wars-api-nginx
docker logs -f star-wars-api-app

# Queue worker logs
docker logs -f star-wars-api-queue
```

### 7. Stop the Stack

```bash
docker-compose -f docker-compose-dev.yml down
```

To also remove volumes (database data):
```bash
docker-compose -f docker-compose-dev.yml down -v
```

---

## 🔨 Option 2: API Only Mode

If you only want to run the API backend (without the frontend):

### 1. Clone the Repository

```bash
git clone git@github.com:flavioalvespro/star-wars-api.git
cd star-wars-api
```

### 2. Configure Environment Variables (API Only)

Copy the example environment file and configure it:

```bash
cp .env.example .env
```

**Important**: Update the following variables in your `.env` file:

```env
# Application
APP_NAME=Star-Wars-API
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database Configuration (Docker)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=star_wars_api
DB_USERNAME=laravel
DB_PASSWORD=secret

# Redis Configuration (Docker)
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=

# Cache, Session & Queue Configuration
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# SWAPI Integration
SWAPI_BASE_URL=https://swapi.tech/api
```

> **Note**: The `SWAPI_BASE_URL` points to the external Star Wars API (SWAPI). This is the third-party service that provides all Star Wars data (characters, films, planets, etc.). No API key is required.

### 3. Generate Application Key (Optional)

If you didn't set `APP_KEY` in `.env`, you can generate it later after starting the containers (see step 5).

### 4. Build and Start Docker Containers

```bash
# Build the containers (first time only)
docker-compose build --no-cache

# Start all services in detached mode
docker-compose up -d
```

> **Note**: Use `--no-cache` to ensure a clean build with all dependencies properly installed.

This will start the following services:
- **app** - Laravel application (PHP-FPM)
- **nginx** - Web server (port 80)
- **mysql** - Database (port 3307 externally)
- **redis** - Cache & Queue
- **queue** - Background worker for processing jobs
- **scheduler** - Laravel task scheduler

### 5. Access the Application Container

Enter the application container to run setup commands:

```bash
docker exec -it star-wars-api sh
```

Now you're inside the container. Run the following commands:

```bash
# Generate application key (if not set in .env)
php artisan key:generate

# Install dependencies
composer install

# Run database migrations
php artisan migrate

# Exit the container
exit
```

### 6. Verify Installation

Check if all containers are running:

```bash
docker-compose ps
```

You should see all services running (status: Up).

## Accessing the Application

Once all containers are running, the API will be available at:

```
http://localhost
```

Test the API with a sample request:

```bash
curl "http://localhost/api/v1/people/search?name=Luke"
```

## Postman Collection

A ready-to-use Postman collection is available in the `postman/` directory for easy API testing.

### Import to Postman

1. Open Postman
2. Click **Import** button
3. Select the files:
   - `postman/Star-Wars-API.postman_collection.json` - All API endpoints
   - `postman/Star-Wars-API.postman_environment.json` - Environment variables
4. Select the **Star Wars API - Local** environment from the dropdown
5. Start testing!

### Collection Contents

The collection includes all endpoints with:
- ✅ Pre-configured requests with examples
- ✅ Descriptions for each endpoint
- ✅ Test cases (success, validation errors, no results)
- ✅ Environment variables for easy configuration

## API Endpoints

All endpoints are prefixed with `/api/v1`

### People (Characters)

#### Search People
```bash
GET /api/v1/people/search?name={searchTerm}
```

**Example:**
```bash
curl "http://localhost/api/v1/people/search?name=Luke"
```

**Response:**
```json
{
  "data": [
    {
      "uid": "1",
      "name": "Luke Skywalker",
      "gender": "male",
      "birth_year": "19BBY",
      "height": "172",
      "mass": "77",
      "hair_color": "blond",
      "skin_color": "fair",
      "eye_color": "blue",
      "homeworld": "https://www.swapi.tech/api/planets/1",
      "url": "https://www.swapi.tech/api/people/1"
    }
  ]
}
```

#### Get Person by ID
```bash
GET /api/v1/people/{id}
```

**Example:**
```bash
curl "http://localhost/api/v1/people/1"
```

### Films

#### Search Films
```bash
GET /api/v1/films/search?title={searchTerm}
```

**Example:**
```bash
curl "http://localhost/api/v1/films/search?title=Hope"
```

**Response:**
```json
{
  "data": [
    {
      "uid": "1",
      "title": "A New Hope",
      "episode_id": 4,
      "opening_crawl": "It is a period of civil war...",
      "director": "George Lucas",
      "producer": "Gary Kurtz, Rick McCallum",
      "release_date": "1977-05-25",
      "characters": [...],
      "planets": [...],
      "starships": [...],
      "vehicles": [...],
      "species": [...],
      "url": "https://www.swapi.tech/api/films/1"
    }
  ]
}
```

#### Get Film by ID
```bash
GET /api/v1/films/{id}
```

**Example:**
```bash
curl "http://localhost/api/v1/films/1"
```

### Statistics

#### Get Search Statistics
```bash
GET /api/v1/statistics
```

**Example:**
```bash
curl "http://localhost/api/v1/statistics"
```

**Response:**
```json
{
  "data": {
    "top_queries": [
      {
        "term": "luke",
        "count": 15,
        "percentage": 50
      },
      {
        "term": "vader",
        "count": 10,
        "percentage": 33.33
      }
    ],
    "average_response_time_ms": 245.67,
    "popular_hours": [
      {
        "hour": 14,
        "count": 20
      },
      {
        "hour": 15,
        "count": 18
      }
    ],
    "total_searches": 30,
    "last_computed_at": "2025-12-23 14:30:45"
  }
}
```

> **Note**: Statistics are automatically recomputed every 5 minutes via a background job using Laravel's queue system.

## Search Analytics & Statistics

### How it Works

1. **Search Logging**: Every search query (people or films) is automatically logged to the `search_logs` table with:
   - Search term
   - Entity type (people/films)
   - Results count
   - Response time in milliseconds
   - Timestamp

2. **Background Processing**: A scheduled job (`ComputeSearchStatistics`) runs every 5 minutes to:
   - Calculate top 5 most searched terms with percentages
   - Compute average API response time
   - Identify most popular hours for searches
   - Store results in the `search_statistics` table

3. **Statistics Endpoint**: The `/api/v1/statistics` endpoint returns the most recently computed statistics

### Queue System

The application uses Redis queues to process background jobs:

- **Queue Worker**: Automatically started via Docker (`star-wars-queue` container)
- **Scheduler**: Runs Laravel's task scheduler to dispatch jobs every 5 minutes

## Running Tests

The application includes comprehensive feature tests covering all endpoints.

### Run Tests

First, access the application container:

```bash
docker exec -it star-wars-api sh
```

Inside the container, run the tests:

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=PeopleTest
php artisan test --filter=FilmTest
php artisan test --filter=SearchStatisticsTest

# Run with coverage
php artisan test --coverage
```

Or run tests directly without entering the container:

```bash
docker exec -it star-wars-api php artisan test
```

## Project Structure

```
star-wars-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # API Controllers
│   │   ├── Requests/           # Form Request Validation
│   │   └── Resources/          # API Resources (response formatting)
│   ├── Jobs/                   # Background Jobs
│   │   └── ComputeSearchStatistics.php
│   ├── Models/                 # Eloquent Models
│   ├── Repositories/           # Data Access Layer
│   │   └── SearchStatisticRepository.php
│   └── Services/               # Business Logic Layer
│       ├── Swapi/              # SWAPI Integration Services
│       └── SearchStatisticService.php
├── database/
│   └── migrations/             # Database Migrations
├── docker/                     # Docker Configuration Files
│   ├── nginx/
│   ├── php/
│   └── mysql/
├── postman/                    # Postman Collection & Environment
│   ├── Star-Wars-API.postman_collection.json
│   └── Star-Wars-API.postman_environment.json
├── routes/
│   └── api.php                 # API Routes
├── tests/
│   └── Feature/                # Feature Tests
└── docker-compose.yml          # Docker Compose Configuration
```

## Docker Services

### Frontend Container (React + Vite)

- **Image**: Custom Node 20 Alpine
- **Port**: 5173
- **Purpose**: React development server with HMR
- **Container Name**: `star-wars-frontend`
- **Environment**: Connects to API via Docker network (`http://api-nginx:80`)
- **Hot Reload**: Enabled via volume mounting
- **Access Logs**: `docker logs -f star-wars-frontend`

**How Frontend Connects to API:**

The frontend uses environment variables to determine the API URL:

```javascript
// In src/services/api.js
baseURL: import.meta.env.VITE_API_URL || 'http://localhost:80'
```

When running via `docker-compose-dev.yml`:
- **Inside Docker**: Frontend uses `http://api-nginx:80` (containers communicate via Docker network)
- **Outside Docker**: Browser accesses `http://localhost:5173` (mapped port)

This setup allows:
- ✅ Containers to communicate internally via service names
- ✅ Browser to access both services on localhost
- ✅ No CORS issues
- ✅ Hot reload for instant development feedback

### Application Container (app)

- **Image**: Custom PHP 8.3-FPM Alpine
- **Purpose**: Runs the Laravel application
- **Container Name**: `star-wars-api`
- **Access**: `docker exec -it star-wars-api sh`

### Nginx Container

- **Image**: Nginx Alpine
- **Port**: 80
- **Purpose**: Web server and reverse proxy

### MySQL Container

- **Image**: MySQL 8.0
- **Internal Port**: 3306
- **External Port**: 3307
- **Database**: `star_wars_api`
- **Username**: `laravel`
- **Password**: `secret`
- **Container Name**: `star-wars-mysql`
- **Access**: `docker exec -it star-wars-mysql mysql -u laravel -p`

### Redis Container

- **Image**: Redis 7 Alpine
- **Port**: 6379
- **Purpose**: Cache, sessions, and queue backend
- **Container Name**: `star-wars-redis`
- **Access**: `docker exec -it star-wars-redis redis-cli`

### Queue Worker Container

- **Purpose**: Processes background jobs from Redis queue
- **Auto-restart**: Yes
- **Logs**: `docker logs -f star-wars-queue`

### Scheduler Container

- **Purpose**: Runs Laravel's task scheduler
- **Schedule**: Checks every minute for scheduled tasks
- **Job**: Dispatches `ComputeSearchStatistics` every 5 minutes

## Environment Variables Reference

| Variable | Description | Default/Example |
|----------|-------------|-----------------|
| `APP_NAME` | Application name | `Star-Wars-API` |
| `APP_ENV` | Environment (local/production) | `local` |
| `APP_DEBUG` | Debug mode | `true` |
| `APP_URL` | Application URL | `http://localhost` |
| `DB_CONNECTION` | Database driver | `mysql` |
| `DB_HOST` | Database host | `mysql` (Docker) |
| `DB_PORT` | Database port | `3306` |
| `DB_DATABASE` | Database name | `star_wars_api` |
| `DB_USERNAME` | Database user | `laravel` |
| `DB_PASSWORD` | Database password | `secret` |
| `REDIS_HOST` | Redis host | `redis` (Docker) |
| `REDIS_PORT` | Redis port | `6379` |
| `CACHE_STORE` | Cache driver | `redis` |
| `SESSION_DRIVER` | Session driver | `redis` |
| `QUEUE_CONNECTION` | Queue driver | `redis` |
| `SWAPI_BASE_URL` | SWAPI endpoint | `https://swapi.tech/api` |

## 🔗 Related Projects

### Frontend Repository
- **[Star Wars Frontend](https://github.com/flavioalvespro/star-wars-frontend)** - React application that consumes this API
- Built with React 19, Vite, and React Router
- Provides search interface for People and Films
- Includes detail pages with navigation between entities

> **Note**: The frontend Docker configuration is included in this repository (`docker-compose-dev.yml`) for easy full-stack development.

## License

This project is open-source and available under the MIT License.

## Contact

For questions or feedback about this technical assessment, please contact the repository owner.
