# MangaDex Backend

A Symfony application using SQLite database.

## Setup

1. Install dependencies:
   ```bash
   composer install
   ```

2. Ensure SQLite PHP extension is installed:
   ```bash
   # On Ubuntu/Debian:
   sudo apt-get install php-sqlite3
   
   # On CentOS/RHEL:
   sudo yum install php-pdo_sqlite
   
   # On macOS (if using Homebrew PHP):
   # SQLite is usually included by default
   ```

3. Create the database file:
   ```bash
   touch data/mangadex.db
   ```

4. Clear cache:
   ```bash
   php bin/console cache:clear
   ```

5. Run the development server:
   ```bash
   php -S localhost:8000 -t public/
   ```

## Database

The application uses SQLite with the database file located at `data/mangadex.db`.

## Migration from Docker/PostgreSQL

This project has been migrated from Docker with PostgreSQL to use SQLite:
- Removed Docker configuration files
- Switched database connection to SQLite
- Updated Doctrine configuration for SQLite
- Database file is created automatically when needed
