# Mehfil Event Planners — Task 2 Laravel 10 + MySQL

This `backend/` is a Laravel 10 **source skeleton** that implements Task 2: migrations, Eloquent relationships, Faker seeders, and schema tests. It accompanies the pre-existing React/Tailwind app in `../frontend/`. Task 3 (REST API) and Task 5 (production authentication/reviews) are **not implemented** here.

## Requirements
- PHP 8.1+ with `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`
- Composer 2
- MySQL 8+ (or compatible MariaDB); a MySQL user with schema permissions
- Node.js/npm to run the React frontend separately

## Database setup

From the MySQL CLI, run (adjust user permissions appropriately):

```sql
CREATE DATABASE mehfil_directory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

From this `backend/` folder:

```bash
composer install
cp .env.example .env             # Windows PowerShell: Copy-Item .env.example .env
php artisan key:generate
# Edit DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env
php artisan migrate --seed
php artisan serve
```

Open http://127.0.0.1:8000 to confirm Laravel boots. **This page is only a backend status/intro page, not the React landing page.**

Check the seeded dataset:

```bash
php artisan tinker
>>> App\Models\Category::count();   // 5
>>> App\Models\Listing::count();    // 10
>>> App\Models\Review::count();     // 20
>>> App\Models\Listing::with(['category','user','reviews'])->first();
```

Use `php artisan migrate:fresh --seed` ONLY when you intend to **delete all current tables/data** from the configured database. Demo users have predictable passwords (`password`) and are **for development only**. The seeded listings are fictional and are not verified venue offers.

## Run tests

`php artisan test` uses an in-memory SQLite database. To run the tests, your PHP installation must also provide `pdo_sqlite`. For MySQL-specific verification, run the migration and seeding commands above.

## Database structure

See [`docs/schema.md`](docs/schema.md) for the entity-relationship diagram. The core tables are `users`, `categories`, `listings`, and `reviews`; a password reset token table, failed jobs table and Sanctum personal access token table are included for Laravel infrastructure.

## Frontend

```bash
cd ../frontend
npm install
npm run dev
```

The original React frontend still reads demo data from its own JavaScript data file. **It is not connected to MySQL yet**; a real Laravel REST API and integration belong to Task 3/4. This is intentional to avoid claiming that the frontend persists changes.

## Notes

- No `.env` or production credentials are included; edit the copied `.env` file locally.
- The full `vendor/` directory is intentionally omitted; Composer installs Laravel dependencies.
- Migration and seeder runtime execution cannot be verified without Composer dependencies and MySQL.
- Schema relationships use indexed foreign keys; review uniqueness is enforced at the database level.
