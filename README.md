# Star Wars API

A Laravel-based REST API that integrates with the [SWAPI (Star Wars API)](https://swapi.tech/) to provide search functionality for Star Wars characters and films, with built-in search analytics and statistics.

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

- **PHP 8.3**
- **Laravel 12**
- **MySQL 8.0** - Main database
- **Redis 7** - Caching, sessions, and queue management
- **Nginx** - Web server
- **Docker & Docker Compose** - Containerized environment

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

### 1. Clone the Repository

```bash
git clone git@github.com:flavioalvespro/star-wars-api.git
cd star-wars-api
```

### 2. Configure Environment Variables

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

## License

This project is open-source and available under the MIT License.

## Contact

For questions or feedback about this technical assessment, please contact the repository owner.
