# Horizon 

**Horizon** is a Laravel-based web application that integrates with the [NewsData.io](https://newsdata.io/) API to provide news filtering by country, category, and language. The project includes Redis caching, token-based pagination, error handling, and automated testing.

---

## 🚀 Quick Start with Docker

### ✅ Prerequisites

Make sure you have the following installed:

- [Docker](https://www.docker.com/)
- [Git](https://git-scm.com/)

> **Note**: You do **not** need to install PHP, Composer, or a local database—everything runs in Docker.

---

### 📦 Clone the Repository

```bash
git clone https://github.com/DavideSuardi/Horizon.git
cd Horizon
```

### ⚙️ Environment Setup

Copy the example environment file:

```bash
cp .env.example .env
```
(Optional) You will generate the application key after the containers are up (see below).

### 🐳 Start the Containers
From the project root, run:

```bash
docker compose up -d --build
```
This will start:
- Laravel app container (available at http://localhost:8000)
- Redis container

### 📂 Install Composer Dependencies
```bash
docker compose exec app composer install
```

### 🧪 Run Migrations & Seeders
```bash
docker compose exec app php artisan migrate --seed
```

### 🔐 Generate the App Key
```bash
docker compose exec app php artisan key:generate
```

### 🌐 Access the App
Open your browser and visit:

```
http://localhost:8000
```

## 📡 API Usage
The API provides several endpoints for working with countries, categories, and retrieving news from the NewsData.io API.


### 🌍 COUNTRIES
```
GET /api/countries
```
Returns a list of all available countries.

#### 📘 Example
```GET /api/countries```

----------------------------

```
GET /api/country/{code}
```
Returns details for a single country by its ISO code.

#### 📘 Example
```GET /api/country/it```

----------------------

```
GET /api/country/{code}/{category}
```
Fetch news from a specific country and category.

#### 📘 Example
```GET /api/country/us/technology```

-------------

```
POST /api/country/{code}/{category}
```
Assign a category to a specific country.

#### 📘 Example
```POST /api/country/fr/sports```
#### Response
- 200 OK if the category was successfully added.
- 409 Conflict if already associated.

--------------------

```
DELETE /api/country/{code}/{category}
```
Remove a category from a specific country.

#### 📘 Example
```DELETE /api/country/de/business```
#### Response
- 200 OK if successfully removed.
- 404 Not Found if association doesn't exist.

----------------------


### 📰 NEWS
```
GET /api/news/{code?}/{page?}
```
Fetch paginated news by country code and page number.
- code is an optional ISO country code
- page is the optional page number (default: 1)

#### 📘 Example
```GET /api/news```

```GET /api/news/us```

```GET /api/news/us/2```

-------------------------

### 🔁 Pagination
If a response contains a nextToken, you can pass it to retrieve the next page of results.

```
GET /api/news?country=us&category=technology&nextToken=SOME_TOKEN
```

### 🧪 Running Tests
To run unit and feature tests:

```bash
docker compose exec app php artisan test
```

## 💡 Technologies Used
- Laravel 10
- PHP 8.2
- Docker & Docker Compose
- Redis (for caching and queue)
- SQLite (for development)
- NewsData.io API


## 📁 Project Structure Highlights
- ```app/Http/Controllers/NewsController.php``` – Handles NewsData.io API logic
- ```routes/api.php``` – Contains the main API routes
- ```database/seeders/``` – Includes seeders for countries, categories, and languages
- ```tests/``` – Unit and feature tests for models and API endpoints


## 🧠 Useful Commands
- Clear cache:

```bash
docker compose exec app php artisan cache:clear
```
- Open an interactive shell inside the container:

```bash
docker compose exec app bash
```

## 📌 Notes
- The application uses Redis for both cache and queue systems.
- SQLite is used as the default database for simplicity during development.
- The NewsController is responsible for fetching and caching news from NewsData.io, with support for filtering and pagination.


