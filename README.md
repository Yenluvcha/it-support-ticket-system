# IT Support Ticket System

A Laravel application for submitting and managing internal IT support tickets. It has separate user and administrator workflows, role-based access control, and queued in-app notifications.

## Features

- Public registration for normal users; administrator accounts are provisioned securely through a seeder.
- Users can create tickets and view only their own ticket history.
- Administrators can view every ticket and progress its status through:
  `Open -> In Progress -> Resolved -> Closed`.
- Responsive DaisyUI dashboard with ticket summaries, ticket lists, and ticket detail pages.
- Queued database notifications:
  - New tickets notify all administrators except the ticket creator.
  - Status changes notify the ticket owner unless they made the change.
  - Users can view and mark notifications as read.

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- SQLite, MySQL, MariaDB, PostgreSQL, or SQL Server

## Installation

1. Install PHP dependencies and frontend dependencies:

   ```bash
   composer install
   npm install
   ```

2. Create your environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   On Windows PowerShell, use:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Configure the database and initial administrator in `.env`:

   ```dotenv
   DB_CONNECTION=sqlite
   QUEUE_CONNECTION=database

   ADMIN_NAME="Support Administrator"
   ADMIN_EMAIL=admin@example.com
   ADMIN_PASSWORD=change-this-password
   ```

4. Run migrations and provision the administrator:

   ```bash
   php artisan migrate
   php artisan db:seed --class=AdminUserSeeder
   ```

5. Build frontend assets and start the application:

   ```bash
   npm run build
   php artisan serve
   ```

## Queue Worker

Ticket notifications are queued in the database. Run a worker alongside the application:

```bash
php artisan queue:work
```

For local development, the Composer `dev` script starts the Laravel server, queue listener, and Vite development server together:

```bash
composer dev
```

## Roles

### User

- Create tickets.
- View their own tickets and status updates.
- View and manage their own notifications.

### Administrator

- View all tickets.
- Advance ticket statuses one step at a time.
- Receive notifications for new tickets created by other users.

Public registration always creates a `user`; never submit `role=admin` through registration. Use `AdminUserSeeder` to create or update an administrator.

## Testing

Run the feature and unit test suite with:

```bash
php artisan test
```

Run the formatter check with:

```bash
vendor/bin/pint --test
```

## Useful Commands

```bash
# Show registered routes
php artisan route:list

# Retry failed queue jobs
php artisan queue:retry all

# Inspect failed queue jobs
php artisan queue:failed
```
