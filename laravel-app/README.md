# Cinevo Laravel Conversion

This Laravel app is the module-by-module replacement for the original Core PHP project.

## Completed module

- Database configuration for the existing `web_db` MySQL database.
- Laravel migrations mirroring the original schema.
- Authentication: register, login, remember me, logout, role-based admin access, inactive-user blocking, forgot/reset password flow.
- Backward-compatible login for existing plain-text passwords. On successful login, the password is rehashed automatically.

## Setup

```bash
cd laravel-app
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

If you already imported `DataBase/web_db.sql`, keep the `.env` database values pointed at `web_db` and run only the support-table migration if needed.
