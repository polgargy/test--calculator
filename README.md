# Admission Score Calculator

A Hungarian university admission score calculator built as a full-stack application with a Laravel REST API and a Vue 3 single-page frontend.

## Table of Contents

- [Tech Stack](#tech-stack)
  - [Backend](#backend-api)
  - [Frontend](#frontend-client)
  - [Infrastructure](#infrastructure)
- [Project Structure](#project-structure)
- [Installation](#installation)
  - [Prerequisites](#prerequisites)
  - [Steps](#steps)
  - [Environment files](#environment-files)
- [Make Commands](#make-commands)
  - [Docker](#docker)
  - [Container Access](#container-access)
  - [Database](#database)
  - [Testing](#testing)
  - [Linting & Code Style](#linting--code-style)
  - [Dependencies](#dependencies)
  - [Installation Helpers](#installation-helpers)
  - [Artisan & Utilities](#artisan--utilities)
  - [Cleanup](#cleanup)
- [API Endpoints](#api-endpoints)
  - [Score calculation rules](#score-calculation-rules)
- [Database Structure](#database-structure)
  - [Entity Relationship Overview](#entity-relationship-overview)
  - [Tables](#tables)
  - [Seed data](#seed-data)
- [Git Hooks](#git-hooks)

---

## Tech Stack

### Backend (`api/`)

| Technology         | Version                                 |
| ------------------ | --------------------------------------- |
| PHP                | 8.3                                     |
| Laravel            | 12.x                                    |
| PostgreSQL         | 18.x                                    |
| PHPStan / Larastan | static analysis                         |
| Laravel Pint       | code style (PSR-12)                     |
| PHPUnit            | feature & unit tests (SQLite in-memory) |

### Frontend (`client/`)

| Technology   | Version      |
| ------------ | ------------ |
| Vue          | 3.5          |
| TypeScript   | 5.9          |
| Vite         | 7.x          |
| Pinia        | 3.x          |
| Vue Router   | 5.x          |
| Tailwind CSS | 4.x          |
| ESLint       | code linting |

### Infrastructure

| Service                    | Host port | Container port |
| -------------------------- | --------- | -------------- |
| Nginx (API + client proxy) | `8080`    | `80`           |
| Vite dev server            | `5173`    | `5173`         |
| PostgreSQL                 | `54321`   | `5432`         |

All services run inside Docker via `docker-compose`.

---

## Project Structure

```
.
├── api/            # Laravel 12 backend
│   ├── app/
│   │   ├── Domain/Calculator/   # DDD domain logic
│   │   │   ├── Enums/
│   │   │   ├── Http/Controllers/
│   │   │   ├── Models/
│   │   │   ├── Repository/
│   │   │   ├── Resource/
│   │   │   └── Service/
│   │   └── Http/
│   │       └── Requests/Calculator/
│   ├── database/migrations/     # Schema + seed data
│   ├── routes/api.php
│   └── tests/Feature/
├── client/         # Vue 3 frontend
│   └── src/
│       ├── api/
│       ├── components/calculator/
│       ├── lib/
│       ├── router/
│       ├── stores/
│       └── views/
├── docker/dev/     # Dockerfile per service
├── scripts/        # Git hooks
├── .env.example    # Docker / root env template
├── .env.api        # Laravel env (copied to api/.env)
├── .env.client     # Vite env (copied to client/.env)
└── Makefile
```

---

## Installation

### Prerequisites

- Docker & Docker Compose
- `make`

### Steps

```bash
# 1. Clone the repository
git clone <repo-url> calculator
cd calculator

# 2. Copy the root environment file
cp .env.example .env

# 3. Run the full installation (build images, copy envs, install dependencies,
#    generate keys, run migrations, install git hooks)
make install

# 4. Seed the database with initial data
make db-fresh-seed
```

After installation the application is available at:

- **Frontend:** http://localhost:5173
- **API:** http://localhost:8080/api

### Environment files

| File           | Purpose                             | Destination                                  |
| -------------- | ----------------------------------- | -------------------------------------------- |
| `.env.example` | Docker / Compose variables template | copy to `.env`                               |
| `.env.api`     | Laravel application config          | copied to `api/.env` by `make env-api`       |
| `.env.client`  | Vite / frontend config              | copied to `client/.env` by `make env-client` |

---

## Make Commands

### Docker

| Command                            | Description                        |
| ---------------------------------- | ---------------------------------- |
| `make up`                          | Start all containers               |
| `make down`                        | Stop all containers                |
| `make build`                       | Build images and start containers  |
| `make build-no-cache`              | Build images without Docker cache  |
| `make rebuild`                     | Stop, rebuild and start containers |
| `make restart`                     | Restart all containers             |
| `make restart-client` / `make rc`  | Restart the client container       |
| `make status` / `make s`           | Show container status              |
| `make logs`                        | Tail all container logs            |
| `make logs-client` / `make logs-c` | Tail client container logs         |

### Container Access

| Command         | Description                              |
| --------------- | ---------------------------------------- |
| `make php`      | Open a shell in the PHP container        |
| `make client`   | Open a shell in the client container     |
| `make postgres` | Open a shell in the PostgreSQL container |

### Database

| Command                 | Description                                     |
| ----------------------- | ----------------------------------------------- |
| `make db-migrate`       | Run pending migrations                          |
| `make db-migrate-force` | Run migrations with `--force` (non-interactive) |
| `make db-rollback`      | Rollback the last migration batch               |
| `make db-seed`          | Run database seeders                            |
| `make db-fresh`         | Drop all tables and re-run all migrations       |
| `make db-fresh-seed`    | Fresh migration + seed                          |

### Testing

| Command                  | Description                             |
| ------------------------ | --------------------------------------- |
| `make test`              | Run all PHP and client tests            |
| `make test-php`          | Run PHP tests (stop on first failure)   |
| `make test-php-all`      | Run all PHP tests                       |
| `make test-php-coverage` | Run PHP tests with HTML coverage report |
| `make test-client`       | Run client tests                        |

PHP tests use an in-memory SQLite database — no separate test DB setup required.

### Linting & Code Style

| Command                | Description                                         |
| ---------------------- | --------------------------------------------------- |
| `make lint`            | Run all linters (PHP + client)                      |
| `make lint-fix`        | Auto-fix all linting issues                         |
| `make lint-php`        | Run Pint (style check) + Larastan (static analysis) |
| `make lint-php-pint`   | Run Laravel Pint in check mode                      |
| `make lint-php-stan`   | Run PHPStan / Larastan                              |
| `make lint-php-fix`    | Run Laravel Pint with auto-fix                      |
| `make lint-client`     | Run ESLint on the client                            |
| `make lint-client-fix` | Run ESLint with auto-fix                            |

### Dependencies

| Command                 | Description                               |
| ----------------------- | ----------------------------------------- |
| `make composer-install` | Install PHP dependencies                  |
| `make composer-update`  | Update PHP dependencies                   |
| `make npm-install`      | Install Node dependencies                 |
| `make npm-update`       | Update Node dependencies                  |
| `make update-deps`      | Update both Composer and npm dependencies |

### Installation Helpers

| Command                          | Description                              |
| -------------------------------- | ---------------------------------------- |
| `make install`                   | Full first-time setup (see Installation) |
| `make env-api`                   | Copy `.env.api` → `api/.env`             |
| `make env-client`                | Copy `.env.client` → `client/.env`       |
| `make key`                       | Generate Laravel app key and JWT secret  |
| `make storage`                   | Create Laravel storage symlink           |
| `make permissions` / `make perm` | Fix storage/cache directory permissions  |
| `make autoload`                  | Regenerate Composer autoload             |
| `make install-hooks`             | Install git pre-commit hook              |

### Artisan & Utilities

| Command            | Description              |
| ------------------ | ------------------------ |
| `make tinker`      | Open Laravel Tinker REPL |
| `make cache-clear` | Clear all Laravel caches |

### Cleanup

| Command               | Description                                                    |
| --------------------- | -------------------------------------------------------------- |
| `make logs-clear`     | Delete nginx and Laravel log files                             |
| `make remove-volumes` | Remove all Docker volumes (destructive)                        |
| `make prune-networks` | Prune unused Docker networks                                   |
| `make clean`          | Full teardown: stop containers, remove volumes, prune networks |

---

## API Endpoints

| Method | Endpoint                   | Description                                       |
| ------ | -------------------------- | ------------------------------------------------- |
| `GET`  | `/api/institutions`        | List institutions with faculties and subjects     |
| `GET`  | `/api/required-subjects`   | List globally required exam subjects              |
| `GET`  | `/api/language-options`    | List available language exam languages and levels |
| `POST` | `/api/calculator`          | Calculate admission score, save student           |
| `GET`  | `/api/calculator/students` | List saved students (supports pagination)         |

### Score calculation rules

- **Base points** = (required subject result + best elective result) × 2
- **Additional points** (capped at 100):
  - Advanced-level exam: +50 per subject
  - B2 language exam: +28 per language
  - C1 language exam: +40 per language (only the highest level counts per language)
- **Total** = base points + additional points

---

## Database Structure

### Entity Relationship Overview

```
subjects ──────────────────────────────────────────────────┐
    │                                                       │
    │ required_subject_id                                   │ elective_subjects
    ▼                                                       │ (pivot table)
faculties ─────────────────────────────┤
    │ institution_id                    │
    ▼                                   │
institutions                        students
                                        │
                            ┌───────────┴──────────┐
                            ▼                      ▼
                         results           language_exams
                          │
                          ▼
                        subjects
```

### Tables

#### `subjects`

| Column                      | Type      | Description                                  |
| --------------------------- | --------- | -------------------------------------------- |
| `id`                        | bigint PK |                                              |
| `name`                      | string    | Exam subject name (e.g. `matematika`)        |
| `required`                  | boolean   | `true` = globally required for all faculties |
| `created_at` / `updated_at` | timestamp |                                              |

#### `institutions`

| Column                      | Type      | Description                       |
| --------------------------- | --------- | --------------------------------- |
| `id`                        | bigint PK |                                   |
| `name`                      | string    | Institution name (e.g. `ELTE IK`) |
| `created_at` / `updated_at` | timestamp |                                   |

#### `faculties`

| Column                      | Type                          | Description                                                  |
| --------------------------- | ----------------------------- | ------------------------------------------------------------ |
| `id`                        | bigint PK                     |                                                              |
| `institution_id`            | bigint FK → `institutions.id` |                                                              |
| `name`                      | string                        | Faculty/programme name                                       |
| `required_subject_id`       | bigint FK → `subjects.id`     | Faculty-specific required exam subject                       |
| `requires_advanced_level`   | boolean                       | Whether the required subject must be taken at advanced level |
| `created_at` / `updated_at` | timestamp                     |                                                              |

#### `elective_subjects` (pivot)

| Column                      | Type                       | Description  |
| --------------------------- | -------------------------- | ------------ |
| `faculty_id`                | bigint FK → `faculties.id` | Composite PK |
| `subject_id`                | bigint FK → `subjects.id`  | Composite PK |
| `created_at` / `updated_at` | timestamp                  |              |

#### `students`

| Column                      | Type                       | Description                            |
| --------------------------- | -------------------------- | -------------------------------------- |
| `id`                        | bigint PK                  |                                        |
| `name`                      | string                     | Student name                           |
| `faculty_id`                | bigint FK → `faculties.id` | Applied faculty                        |
| `base_points`               | integer                    | Calculated base score                  |
| `additional_points`         | integer                    | Calculated bonus score (capped at 100) |
| `created_at` / `updated_at` | timestamp                  |                                        |

#### `results`

| Column                      | Type                      | Description                     |
| --------------------------- | ------------------------- | ------------------------------- |
| `id`                        | bigint PK                 |                                 |
| `student_id`                | bigint FK → `students.id` |                                 |
| `subject_id`                | bigint FK → `subjects.id` |                                 |
| `advanced_level`            | boolean                   | Whether taken at advanced level |
| `result`                    | integer                   | Score percentage (0–100)        |
| `created_at` / `updated_at` | timestamp                 |                                 |

#### `language_exams`

| Column                      | Type                      | Description                             |
| --------------------------- | ------------------------- | --------------------------------------- |
| `id`                        | bigint PK                 |                                         |
| `student_id`                | bigint FK → `students.id` |                                         |
| `language`                  | string                    | Language code — see `Language` enum     |
| `level`                     | string                    | `B2` or `C1` — see `LanguageLevel` enum |
| `created_at` / `updated_at` | timestamp                 |                                         |

### Seed data

All seed data is inserted inside the migration files themselves (no separate seeders needed). Run `make db-fresh` to reset and re-seed everything.

**Globally required subjects** (`required = true`)

| id  | name                     |
| --- | ------------------------ |
| 1   | magyar nyelv és irodalom |
| 2   | történelem               |
| 3   | matematika               |

**Institutions & faculties**

| Institution | Faculty                     | Required subject | Advanced level |
| ----------- | --------------------------- | ---------------- | -------------- |
| ELTE IK     | Programtervező informatikus | matematika       | No             |
| PPKE BTK    | Anglisztika                 | angol            | Yes            |

**Elective subjects per faculty**

| Faculty                               | Elective subjects                                 |
| ------------------------------------- | ------------------------------------------------- |
| Programtervező informatikus (ELTE IK) | biológia, fizika, informatika, kémia              |
| Anglisztika (PPKE BTK)                | történelem, francia, német, olasz, orosz, spanyol |

---

## Git Hooks

A pre-commit hook is installed by `make install-hooks`. It runs before every commit:

1. **Laravel Pint** — enforces PSR-12 code style
2. **Larastan** (PHPStan level configured in `api/phpstan.neon`) — static analysis
3. **ESLint** — TypeScript/Vue linting

If any check fails the commit is blocked. To bypass (not recommended):

```bash
git commit --no-verify
```
