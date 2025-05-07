# Multi-Tenant Management Guide

## Overview

This application is built with multi-tenancy support using the stancl/tenancy package. Each tenant has its own database and can be accessed via a unique domain.

## Creating a New Tenant

### Using the Interactive Command

The application provides an interactive command to create new tenants. Run this command inside the Docker container:

```bash
docker-compose exec app php artisan tenant:create
```

The command will guide you through the following steps:

1. **Enter tenant name (ID)**: This is a unique identifier for the tenant
2. **Enter tenant domain**: The domain name for the tenant (without .localhost)
3. **Should this tenant be active?**: Choose whether the tenant should be active immediately

### Example Session

```
===== Tenant Creation Wizard =====
Enter tenant name (ID): one
Enter tenant domain (without .localhost): one
Should this tenant be active? (yes/no) [yes]: yes
Creating tenant...
Tenant created successfully!

+------+----------------+--------+
| ID   | Domain         | Active |
+------+----------------+--------+
| one | one.localhost | Yes    |
+------+----------------+--------+

You can access this tenant at: http://one.localhost:9000
```

## Accessing Tenants

After creating a tenant, you can access it using the domain you specified:

```
http://[tenant-domain].localhost:9000
```

Make sure your local hosts file is configured to resolve these domains to your localhost, or use a local DNS resolver.

## Tenant Database Structure

Each tenant has:

1. A record in the `tenants` table with:
   - `id`: The tenant identifier
   - `active`: Whether the tenant is active

2. A record in the `domains` table with:
   - `domain`: The domain name for accessing the tenant
   - `tenant_id`: Reference to the tenant

## Troubleshooting

### Domain Already Exists

If you see an error that the domain already exists, you'll need to choose a different domain name or remove the existing domain from the database.

### Tenant ID Already Exists

If you see an error that the tenant ID already exists, you'll need to choose a different tenant ID or remove the existing tenant from the database.

### Connection Issues

If you're having trouble connecting to a tenant's domain:

1. Ensure the Docker containers are running
2. Check that your local hosts file has the appropriate entries
3. Verify the tenant is marked as active in the database
4. Make sure you're using the correct port (9000)

## Running Commands for a Specific Tenant

To run commands for a specific tenant, use the `tenants:run` command:

```bash
docker-compose exec app php artisan tenants:run [command] --tenant=[tenant-id]
```

For example, to run migrations for a specific tenant:

```bash
docker-compose exec app php artisan tenants:run migrate --tenant=one
```