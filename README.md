# Mehfil Event Planners API — Task 3

Laravel 10 REST API for venue listings and customer reviews. The API returns JSON, validates write requests, protects mutations with Laravel Sanctum bearer tokens, and includes PHPUnit feature tests for success and failure cases.

## Requirements

- PHP 8.1+ and Composer 2
- PHP extensions: `pdo_mysql`, `pdo_sqlite` (tests), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`
- MySQL 8+ / MariaDB for local application data
- Optional: Xdebug or PCOV to generate a coverage report

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create a MySQL database named `mehfil_directory`, then set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`.

```bash
php artisan migrate --seed
php artisan serve
```

The API is available at `http://127.0.0.1:8000/api`.

## Authentication

Register or log in to receive a Sanctum personal access token:

```http
POST /api/register
Content-Type: application/json

{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}
```

```http
POST /api/login
Content-Type: application/json

{"email":"test@example.com","password":"password123"}
```

Send the returned token on protected requests:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

## API endpoints

| Method | Endpoint | Access | Purpose |
|---|---|---|---|
| POST | `/api/register` | Public, rate limited | Create account and issue token |
| POST | `/api/login` | Public, rate limited | Authenticate and issue token |
| GET | `/api/listings` | Public | Paginated active listings; supports `search`, `city`, `category_id`, `per_page` |
| GET | `/api/listings/{id}` | Public | Listing details |
| GET | `/api/categories` | Public | Listing categories |
| GET | `/api/listings/{id}/reviews` | Public | Paginated approved reviews |
| POST | `/api/listings` | Token | Create a listing; authenticated user becomes owner |
| PUT/PATCH | `/api/listings/{id}` | Owner or admin | Update listing |
| DELETE | `/api/listings/{id}` | Owner or admin | Delete listing |
| POST | `/api/listings/{id}/reviews` | Token | Create/update own review (awaits moderation) |
| PUT/PATCH | `/api/reviews/{id}` | Review owner or admin | Update review; resets moderation approval |
| DELETE | `/api/reviews/{id}` | Review owner or admin | Delete review |
| GET | `/api/me` | Token | Current user |
| POST | `/api/logout` | Token | Revoke current token |

### Example: paginated listings

`GET /api/listings?city=Rawalpindi&per_page=5`

Returns Laravel's paginator JSON structure with `data`, `current_page`, `last_page`, `per_page`, `total`, and navigation links. `per_page` is limited to 1–50. Inactive listings are never returned publicly.

### Example: submit a review

`POST /api/listings/1/reviews`

```json
{"rating":5,"title":"Excellent venue","comment":"The staff and facilities were excellent."}
```

A valid review is saved with `is_approved=false`; it is not visible in public review results until moderation approves it. Ratings must be integers from 1 to 5 and comments must contain 10–3000 characters.

### Authorization rules

- Listing create/update/delete requires a valid Sanctum token. A user can update or delete their own listing; an admin can manage any listing.
- Reviews require a token to create. A user can update or delete their own review; an admin can manage any review.
- Public listing and review read endpoints expose active listings and approved reviews only.
- API validation failures return HTTP 422 JSON; unauthenticated requests return 401; forbidden ownership actions return 403; missing records return 404.

## Automated tests

The PHPUnit feature suite uses an in-memory SQLite database and `RefreshDatabase`; it does not touch the configured MySQL database.

```bash
php artisan test
```

For a line coverage report, install/enable Xdebug or PCOV in the PHP environment, then run:

```bash
php artisan test --coverage --min=80
```

If the coverage driver is unavailable, PHPUnit can still run the tests, but it cannot calculate or certify the 80% coverage requirement. Keep the generated coverage report with your submission when available.

## Postman

Import `docs/Mehfil-Task-3.postman_collection.json`. Set the collection variable `base_url` to `http://127.0.0.1:8000` and `token` to the bearer token returned by login/register. The collection includes public reads, authenticated listing/review writes, and validation checks.

## Deployment notes

- Set `APP_ENV=production`, `APP_DEBUG=false`, a strong `APP_KEY`, production database credentials, and the correct `APP_URL`.
- Keep `.env` and credentials out of Git. Configure HTTPS, CORS origins, writable `storage` and `bootstrap/cache`, and database backups.
- Deploy with `composer install --no-dev --optimize-autoloader` and run `php artisan migrate --force`.
- This project is based on Laravel 10 as specified in the existing project. Laravel 10 is end-of-support; upgrade to a supported Laravel release and review dependency advisories before exposing it to real users.
- Venue seed data is fictional sample data. Confirm ownership and venue details before public business use.
