# ABC Project Setup Guide

## Overview

This repository contains a Laravel 12 application with Docker setup for easy development. The stack includes:

- PHP 8.3
- MySQL 8.0
- Nginx
- phpMyAdmin

## Prerequisites

- Docker and Docker Compose installed on your machine
- Git
- Basic knowledge of Laravel and Docker

## Project Structure

The project uses Docker containers with the following services:

- **app**: PHP 8.3 service running Laravel
- **webserver**: Nginx web server
- **db**: MySQL 8.0 database
- **phpmyadmin**: Database management tool

## Port Configuration

- Laravel application: http://localhost:9000
- phpMyAdmin: http://localhost:9001
- PHP-FPM: 9002 (internal service)
- MySQL: 3306

## Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/snlt11/abc.git
cd abc
```

### 2. Configure Environment Variables

The project includes a pre-configured `.env` file with database settings already set up for Docker:

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=abc
DB_USERNAME=root
DB_PASSWORD=root
```

If you need to make changes, edit the `.env` file and the corresponding values in `docker-compose.yml`.

### 3. Build and Start Docker Containers

```bash
docker-compose up -d --build
```

This command builds the PHP image and starts all containers in detached mode.

### 4. Install Composer Dependencies

```bash
docker-compose exec app composer install
```

### 5. Generate Application Key (if not already set)

```bash
docker-compose exec app php artisan key:generate
```

### 6. Run Database Migrations

```bash
docker-compose exec app php artisan migrate
```

### 7. Seed the Database (Optional)

```bash
docker-compose exec app php artisan db:seed
```

## Multi-Tenancy Support

This application supports multi-tenancy using the stancl/tenancy package. For detailed instructions on creating and managing tenants, see [TENANT_GUIDE.md](./TENANT_GUIDE.md).

## Accessing the Application

- **Laravel Application**: http://localhost:9000
- **phpMyAdmin**: http://localhost:9001 (Server: db, Username: root, Password: root)

## File Upload Configuration

The application is configured to handle large file uploads:

- Maximum upload size: 2GB
- Maximum POST size: 2GB
- PHP memory limit: 2GB
- MySQL max allowed packet: 2GB

## Common Docker Commands

### View Running Containers

```bash
docker-compose ps
```

### View Container Logs

```bash
docker-compose logs
```

For a specific service:

```bash
docker-compose logs app
```

### Stop Containers

```bash
docker-compose down
```

### Restart Containers

```bash
docker-compose restart
```

### Execute Commands in Containers

```bash
docker-compose exec app bash
```

## Troubleshooting

### Port Conflicts

If you encounter port conflicts, modify the port mappings in `docker-compose.yml`.

### Permission Issues

If you encounter permission issues, ensure that the Docker user has appropriate permissions:

```bash
chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache
```
