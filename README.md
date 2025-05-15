# ABC Project

## Overview

A multi-tenant Laravel 12 application with GraphQL API support, built with modern development practices and Docker for containerization.

### Tech Stack

- **Backend**: PHP 8.3, Laravel 12
- **API**: GraphQL (Lighthouse)
- **Database**: MySQL 8.0
- **Web Server**: Nginx
- **Containerization**: Docker & Docker Compose
- **Multi-tenancy**: stancl/tenancy

## Prerequisites

- Docker and Docker Compose
- Git
- Composer (for local development)
- Basic knowledge of Laravel and GraphQL

## Project Structure

```
.
├── app/                    # Application code
│   ├── Console/            # Custom Artisan commands
│   ├── Enums/              # PHP Enums
│   ├── GraphQL/            # GraphQL resolvers and types
│   ├── Helpers/            # Helper functions
│   ├── Http/               # Controllers, Middleware, Requests
│   ├── Models/             # Eloquent models
│   ├── Providers/          # Service providers
│   ├── Repositories/       # Data access layer
│   └── Services/           # Business logic
├── config/                 # Configuration files
├── database/               # Migrations, seeders, factories
├── graphql/                # GraphQL schema definitions
│   ├── module/             # Module-related schemas
│   ├── permission/         # Permission schemas
│   └── user/               # User-related schemas
├── public/                 # Publicly accessible files
├── resources/              # Views, lang files
├── routes/                 # Route definitions
│   ├── api.php            # API routes
│   ├── console.php        # Console routes
│   ├── tenant.php         # Tenant web routes
│   ├── tenant-api.php     # Tenant API routes
│   └── web.php            # Web routes
└── tests/                  # Test files
```

### Docker Services

- **app**: PHP 8.3 with Laravel
- **webserver**: Nginx web server
- **db**: MySQL 8.0 database
- **phpmyadmin**: Database management interface

## Service URLs

- **Laravel Application**: http://localhost:9000
- **phpMyAdmin**: http://localhost:9001
- **GraphiQL**: http://localhost:9000/graphiql

### Internal Ports

- **PHP-FPM**: 9002
- **MySQL**: 3306

## Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/snlt11/abc.git
cd abc
```

### 2. Configure Environment

Copy the example environment file and update as needed:

```bash
cp .env.example .env
```

Update these key values in `.env`:

```env
APP_NAME=ABC
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:9000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=abc
DB_USERNAME=root
DB_PASSWORD=root

```

### 3. Start the Application

```bash
docker-compose up -d --build
```

### 4. Install Dependencies

```bash
docker-compose exec app composer install
```

### 5. Generate Application Key

```bash
docker-compose exec app php artisan key:generate
```

### 6. Run Database Migrations

First, run the central database migrations:

```bash
docker-compose exec app php artisan migrate
```

### 7. Create Your First Tenant

```bash
docker-compose exec app php artisan tenant:create
```

Follow the prompts to create your first tenant. You can then access it at `http://[tenant-name].localhost:9000`

### 8. Run Tenant Migrations

After creating a tenant, run migrations for that tenant:

```bash
docker-compose exec app php artisan tenants:run migrate --tenant=[tenant-id]
```

## Documentation

### Multi-Tenancy

This application implements multi-tenancy using the `stancl/tenancy` package. Each tenant has its own database and can be accessed via a unique subdomain.

📖 [View Multi-Tenancy Guide](./TENANT_GUIDE.md)

### GraphQL API

The application exposes a GraphQL API for all data operations. The API is built using the Lighthouse package.

📖 [View GraphQL Architecture Guide](./GraphQL_Architecture_Guide.md)

#### Available GraphQL Endpoints

- **GraphiQL**: http://localhost:9000/graphiql

#### Example Query

```graphql
query {
  users {
    data {
      id
      name
      email
    }
  }
}
```

## Development

### Clearing Caches

```bash
docker-compose exec app php artisan optimize

docker-compose exec app php artisan cache:clear

docker-compose exec app php artisan config:clear

docker-compose exec app php artisan route:clear

docker-compose exec app php artisan view:clear
```

### Viewing Logs

```bash
docker-compose logs -f app
```

## Accessing Services

- **Laravel Application**: http://localhost:9000
- **phpMyAdmin**: http://localhost:9001
  - Server: `db`
  - Username: `root`
  - Password: `root`

## Configuration

### File Uploads

The application is configured to handle large file uploads:

- **Maximum upload size**: 2GB
- **Maximum POST size**: 2GB
- **PHP memory limit**: 2GB
- **MySQL max allowed packet**: 2GB

## Docker Commands

### Container Management

| Command | Description |
|---------|-------------|
| `docker-compose up -d` | Start all services in detached mode |
| `docker-compose down` | Stop and remove all containers |
| `docker-compose ps` | List running containers |
| `docker-compose logs -f [service]` | View logs (use `-f` to follow) |
| `docker-compose restart [service]` | Restart a specific service |
| `docker-compose exec app bash` | Open shell in app container |
| `docker-compose exec db bash` | Open shell in database container |

### Service Logs

```bash
# App logs
docker-compose logs -f app

# Database logs
docker-compose logs -f db

# Web server logs
docker-compose logs -f webserver
```

### Database Access

Connect to MySQL:

```bash
docker-compose exec db mysql -u root -proot
```

### Running Artisan Commands

```bash
docker-compose exec app php artisan [command]
```

## Troubleshooting

### Port Conflicts

If you encounter port conflicts, update the port mappings in `docker-compose.yml`:

```yaml
services:
  app:
    ports:
      - "9000:80"  # Change the first number to an available port
```

### Permission Issues

Fix file permissions:

```bash
# Set proper ownership
sudo chown -R $USER:$USER .

# Make artisan executable
chmod +x artisan

# Set storage and bootstrap/cache permissions
chmod -R 775 storage bootstrap/cache
```

### Common Issues

1. **Container not starting**: Check logs with `docker-compose logs [service]`
2. **Database connection issues**: Verify credentials in `.env` match `docker-compose.yml`
3. **Tenant not found**: Ensure the domain is correctly added to your hosts file
4. **Composer install fails**: Try running with `--ignore-platform-reqs`

## Support

For issues not covered here, please open an issue on our [GitHub repository](https://github.com/snlt11/abc/issues).
