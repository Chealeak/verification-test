# Verification API

Laravel-based RESTful API project.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Migration](#database-migration)
- [Running the Server](#running-the-server)
- [API Documentation](#api-documentation)
- [Testing](#testing)

## Requirements

- PHP 8.0 or higher
- Composer
- Laravel 11
- MySQL or any other database supported by Laravel
- Git

## Installation

1. **Clone the repository:**

   ```bash
   cd your-project
   git clone https://github.com/Chealeak/verification-test.git .

2. **Install dependencies:**

   ```bash
   composer install

3. **Create a copy of the .env file:**

   ```bash
   cp .env.example .env

4. **Generate an application key:**

   ```bash
   php artisan key:generate

## Configuration

1. **Database Configuration:**

Open the `.env` file and update the following lines to match your database configuration:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

2. **Other Environment Variables:**

Configure other environment variables as needed (e.g., cache, queue, mail settings).

## Database Migration

Run the following command to migrate the database:

```bash
php artisan migrate
```

## Running the Server

Start the development server using the following command:

```bash
php artisan serve
```

## API Documentation

1. **Accessing Swagger UI:**

Once the server is running, you can access the Swagger documentation at:

```bash
http://localhost:8000/api/documentation
```

2. **Updating Swagger Documentation:**

If you make changes to your API routes or controllers, ensure the Swagger documentation is updated:

```bash
php artisan l5-swagger:generate
```

## Testing

To run tests, use the following command:

```bash
php artisan test
```
