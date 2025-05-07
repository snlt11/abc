# GraphQL Architecture Guide

## Request Flow

This document explains how a GraphQL request flows through our multi-tenant application, from schema definition to database and back.

## 1. GraphQL Schema Files

Starting point: `/graphql` directory

- Contains `.graphql` files that define the API structure
- Example: `/graphql/user/user.graphql` defines User types, queries, and mutations
- These files define what data can be requested and what operations can be performed

## 2. GraphQL Resolvers

Next stop: `/app/GraphQL` directory

- Contains `Mutations` and `Queries` subdirectories
- Each resolver maps to a specific operation in the schema
- Resolvers receive the GraphQL request and extract arguments
- They don't contain business logic but delegate to Services

## 3. Services Layer

Next: `/app/Services` directory

- Contains business logic for the application
- Validates input data
- Orchestrates complex operations
- Calls repositories to interact with the database
- May call other services when needed

## 4. Repository Layer

Next: `/app/Repositories` directory

- Split into `Contracts` (interfaces) and `Eloquent` (implementations)
- Abstracts database operations
- Implements methods like create, update, delete, find, etc.
- Uses Laravel Eloquent models to interact with the database

## 5. Models

Final stop: `/app/Models` directory

- Represent database tables
- Define relationships between entities
- Handle attribute casting and mutators
- Connect to the appropriate database (tenant-specific in multi-tenant setup)

## Multi-Tenant Context

The entire request flows through tenant-specific middleware:

- When a request comes to `one.localhost:9000/api/graphql`
- The `IdentifyTenant` middleware identifies the tenant from the subdomain
- It configures the database connection to use the tenant's database
- All subsequent database operations use this connection

## Example Request Flow

1. Client sends a mutation to create a user: `POST /api/graphql`
2. Request passes through tenant middleware, setting up the database connection
3. Lighthouse GraphQL processes the request and identifies the resolver
4. The resolver (e.g., `CreateUserMutation`) receives the request
5. Resolver calls the appropriate service (e.g., `UserService->createUser()`)
6. Service validates the data and calls the repository (e.g., `UserRepository->create()`)
7. Repository uses the User model to create a record in the tenant database
8. Response travels back up the chain to the client

## Directory Dependencies

- GraphQL schema files depend on nothing
- Resolvers depend on Services
- Services depend on Repositories
- Repositories depend on Models
- Models depend on database configuration

## Detailed Folder Structure

project-root/
├── graphql/                           # GraphQL schema definitions
│   ├── schema.graphql                 # Main schema file
│   └── user/                          # Domain-specific schemas
│       └── user.graphql               # User type definitions and operations
│
├── app/
│   ├── GraphQL/                       # GraphQL resolvers
│   │   ├── Mutations/                 # Mutation resolvers
│   │   │   └── User/                  # User-related mutations
│   │   │       ├── CreateUserMutation.php
│   │   │       ├── UpdateUserMutation.php
│   │   │       └── DeleteUserMutation.php
│   │   └── Queries/                   # Query resolvers
│   │       └── User/                  # User-related queries
│   │           ├── UserQuery.php
│   │           └── UserListQuery.php
│   │
│   ├── Services/                      # Business logic layer
│   │   └── User/                      # User-related services
│   │       └── UserService.php
│   │
│   ├── Repositories/                  # Data access layer
│   │   ├── Contracts/                 # Repository interfaces
│   │   │   └── UserRepositoryInterface.php
│   │   └── Eloquent/                  # Eloquent implementations
│   │       └── UserRepository.php
│   │
│   ├── Models/                        # Database models
│   │   └── User.php
│   │
│   └── Http/
│       └── Middleware/                # HTTP middleware
│           └── IdentifyTenant.php     # Tenant identification middleware
│
└── config/
└── lighthouse.php                 # GraphQL configuration

This layered architecture ensures separation of concerns and makes the codebase more maintainable and testable.
        