# Multi-Tenant Management Guide

## Overview

This application implements multi-tenancy using the `stancl/tenancy` package, where each tenant operates in complete isolation with its own database. This guide covers all aspects of tenant management in the application.

### Key Features

- **Isolated Databases**: Each tenant has its own dedicated database
- **Domain-based Tenancy**: Tenants are identified by their unique subdomain
- **Central Management**: Central database manages tenant information
- **Automated Provisioning**: Commands for easy tenant management

## Tenant Management Commands

### 1. Creating a New Tenant

#### Interactive Creation

Create a new tenant with an interactive wizard:

```bash
docker-compose exec app php artisan tenant:create
```

The wizard will guide you through:

1. **Tenant ID**: A unique identifier (alphanumeric, dashes, underscores only)
2. **Domain**: Subdomain for the tenant (without .localhost)
3. **Active Status**: Whether the tenant should be immediately active

#### Example Session

```bash
===== Tenant Creation Wizard =====
Enter tenant name (ID): one-company
Enter tenant domain (without .localhost): one
Should this tenant be active? (yes/no) [yes]: yes
Creating tenant...
Tenant created successfully!

+-----------+----------------+--------+
| ID         | Domain        | Active|
+-----------+----------------+--------+
|one-company | one.localhost | Yes    |
+-----------+----------------+--------+

You can access this tenant at: http://one.localhost:9000
```

### 2. Deleting a Tenant

Permanently remove a tenant and all its data:

```bash
docker-compose exec app php artisan tenant:delete
```

This will:
1. Prompt for the tenant ID to delete
2. Show a confirmation prompt with tenant details
3. Delete the tenant record and associated database
4. Clean up any related resources

### 3. Managing Tenant Migrations

#### Create a New Migration

Create a new migration for tenant databases:

```bash
docker-compose exec app php artisan make:migration:tenant create_table_name
```

Options:
- `--create=table_name` - Create a new table
- `--table=table_name` - Modify an existing table

#### Run Migrations

Run migrations for all tenants:

```bash
docker-compose exec app php artisan tenants:migrate
```

Run migrations for a specific tenant:

```bash
docker-compose exec app php artisan tenants:run migrate --tenant=tenant-id
```

## Accessing Tenants

### Web Access

After creating a tenant, access it via:

```bash
http://[tenant-domain].localhost:9000
```


### API Access

Access tenant-specific API endpoints:

```bash
http://[tenant-domain].localhost:9000/api/endpoint
```


### Database Access

Each tenant gets its own database with the naming convention: `tenant_[tenant_id]`

Connect to a tenant's database using:
```bash
docker-compose exec db mysql -u root -proot tenant_[tenant_id]
```

## Tenant Database Structure

### Central Database
- `tenants` table:
  - `id`: Unique tenant identifier
  - `active`: Boolean indicating if tenant is active
  - `created_at`: Creation timestamp
  - `updated_at`: Last update timestamp

- `domains` table:
  - `domain`: Full domain name (e.g., one.localhost)
  - `tenant_id`: Reference to tenants.id

### Tenant Database
Each tenant database contains its own set of tables, completely isolated from other tenants.

## Advanced Usage

### Running Commands for Specific Tenants

Run any Artisan command for a specific tenant:

```bash
docker-compose exec app php artisan tenants:run [command] --tenant=[tenant-id]
```

Examples:

```bash
# Run seeders for a tenant
docker-compose exec app php artisan tenants:run db:seed --tenant=one-company

# Clear cache for a tenant
docker-compose exec app php artisan tenants:run cache:clear --tenant=one-company

# Run custom commands
docker-compose exec app php artisan tenants:run your:command --tenant=one-company
```

### Listing All Tenants

List all tenants with their status:

```bash
docker-compose exec app php artisan tenants:list
```

### Tenant Maintenance Mode

Put a tenant in maintenance mode:

```bash
docker-compose exec app php artisan tenants:run down --tenant=one-company
```

Take a tenant out of maintenance mode:

```bash
docker-compose exec app php artisan tenants:run up --tenant=one-company
```

## Troubleshooting

### Common Issues


#### Domain Already Exists
```bash
The domain has already been taken.
```

**Solution**: Choose a different domain name or delete the existing domain.


#### Tenant ID Already Exists
```bash
The id has already been taken.
```

**Solution**: Use a different tenant ID or delete the existing tenant.


#### Connection Issues
If you can't access a tenant:
1. Verify Docker containers are running:
   ```bash
   docker-compose ps
   ```
2. Check the application logs:
   ```bash
   docker-compose logs app
   ```
3. Verify the tenant is active in the database:
   ```sql
   SELECT * FROM tenants WHERE id = 'tenant-id';
   ```
4. Check your hosts file has the correct entry:
   ```bash
   127.0.0.1   tenant-domain.localhost
   ```


#### Migration Issues
If tenant migrations fail:
1. Check the tenant's database exists
2. Verify database credentials in `.env`
3. Check migration status:
   ```bash
   docker-compose exec app php artisan tenants:run migrate:status --tenant=tenant-id
   ```

4. Run migrations with verbose output:

   ```bash
   docker-compose exec app php artisan tenants:run migrate --tenant=tenant-id -v
   ```


## Best Practices

1. **Naming Conventions**:
   - Use lowercase, hyphen-separated names for tenant IDs
   - Keep domain names simple and relevant to the tenant

2. **Backup Strategy**:
   - Regularly backup tenant databases
   - Consider implementing automated backup solutions

3. **Resource Management**:
   - Monitor database sizes
   - Implement database cleanup routines for old data

4. **Development Workflow**:
   - Use separate environments for development, staging, and production
   - Test tenant creation and migrations in staging before production


## Support

For issues not covered in this guide, please refer to:

1. [Stancl Tenancy Documentation](https://tenancyforlaravel.com/)
2. [Laravel Documentation](https://laravel.com/docs)
3. [Project GitHub Issues](https://github.com/snlt11/abc/issues)