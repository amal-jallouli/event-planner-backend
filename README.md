# Event Planner – Backend API (Laravel)

## Stack
- Laravel 11 + Laravel Sanctum
- MySQL
- PHP >= 8.2

---

## Installation

### 1. Create a fresh Laravel project
```bash
composer create-project laravel/laravel event-planner-backend
cd event-planner-backend
```

### 2. Copy the files from this ZIP into the project
Overwrite all existing files when prompted.

### 3. Install Sanctum
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 4. Configure .env
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your DB credentials:
```
DB_DATABASE=event_planner
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Run migrations + seeders
```bash
php artisan migrate --seed
php artisan storage:link
```

### 6. Start the server
```bash
php artisan serve
# API available at http://localhost:8000/api/v1
```

---

## ⚠️ Laravel 10 users only
In `app/Http/Kernel.php`, add inside `$routeMiddleware`:
```php
'admin' => \App\Http\Middleware\IsAdmin::class,
```
And replace `bootstrap/app.php` with the default Laravel 10 version.

---

## Default Credentials
| Role  | Email                     | Password   |
|-------|---------------------------|------------|
| Admin | admin@eventplanner.com    | password   |
| User  | user@eventplanner.com     | password   |

---

## API Endpoints

### Auth
| Method | URL                    | Description        |
|--------|------------------------|--------------------|
| POST   | /api/v1/auth/register  | Register           |
| POST   | /api/v1/auth/login     | Login              |
| POST   | /api/v1/auth/logout    | Logout (auth)      |
| GET    | /api/v1/auth/me        | Current user (auth)|

### Events
| Method | URL                            | Auth     |
|--------|--------------------------------|----------|
| GET    | /api/v1/events                 | Public   |
| GET    | /api/v1/events/{id}            | Public   |
| POST   | /api/v1/events                 | Admin    |
| POST   | /api/v1/events/{id}/update     | Admin    |
| DELETE | /api/v1/events/{id}            | Admin    |

### Categories
| Method | URL                            | Auth     |
|--------|--------------------------------|----------|
| GET    | /api/v1/categories             | Public   |
| POST   | /api/v1/categories             | Admin    |
| PUT    | /api/v1/categories/{id}        | Admin    |
| DELETE | /api/v1/categories/{id}        | Admin    |

### Registrations
| Method | URL                                  | Auth  |
|--------|--------------------------------------|-------|
| POST   | /api/v1/registrations/{event}        | User  |
| DELETE | /api/v1/registrations/{event}        | User  |
| GET    | /api/v1/registrations/my             | User  |
| GET    | /api/v1/registrations/check/{event}  | User  |
| GET    | /api/v1/registrations                | Admin |

### Dashboard & Profile
| Method | URL                       | Auth  |
|--------|---------------------------|-------|
| GET    | /api/v1/dashboard/stats   | Admin |
| GET    | /api/v1/user/profile      | User  |
| POST   | /api/v1/user/profile      | User  |
| POST   | /api/v1/user/change-password | User |

---

## Event Filters (GET /api/v1/events)
| Param        | Example            |
|--------------|--------------------|
| search       | ?search=tech       |
| category_id  | ?category_id=1     |
| status       | ?status=actif      |
| date_from    | ?date_from=2024-01-01 |
| date_to      | ?date_to=2024-12-31   |
| is_free      | ?is_free=true      |
| per_page     | ?per_page=9        |

---

## Image Upload
Events and profiles support image upload via `multipart/form-data`.
Images are stored in `storage/app/public/events/` and served via `/storage/events/filename.jpg`.
Run `php artisan storage:link` once to enable public access.
