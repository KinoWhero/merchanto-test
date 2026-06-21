# Merchanto Test Assignment

## Overview

This project is a modular e-commerce application built with **Laravel 13**, **Livewire 4**, **Filament 5**, and **Laravel Modules**.

The application demonstrates:

- Modular architecture using Laravel Modules
- Product catalog management
- Order management
- Cross-module communication
- Admin interface built with Filament
- Public product browsing
- Automated code quality checks
- Automated testing
- CI/CD with GitHub Actions

---

## Architecture

The application is divided into independent modules.

### Catalog Module

**Responsibilities:**

- Product categories
- Products 
- Inventory management
- Public catalog browsing

**Entities:**

- ProductCategory
- Product

### Order Module

**Responsibilities:**

- Orders
- Order items
- Order management
- Order confirmation workflow

**Entities:**

- Order
- OrderItem

### Cross-Module Communication:
The Order module requires access to product information but does not directly depend on Catalog models.

To achieve this, the application introduces an application-level contract:

```text
app/Contracts/CatalogInterface.php
```
The Catalog module provides the implementation, while the Order module consumes the abstraction.

This allows:
- Loose coupling between modules
- Independent module evolution
- Easier future extraction into separate services
- Clear ownership of inventory operations

```text
Order Module
       │
       ▼
CatalogInterface
       ▲
       │
Catalog Module
```
The contract currently provides:

- Product snapshot retrieval
- Product availability checks
- Inventory reduction during order confirmation
---

## Design Decisions

### Product Snapshot Strategy

Orders store product information as a snapshot:

- `product_id`
- `product_name`
- `unit_price`

instead of directly depending on Catalog models.

**Benefits:**

- Historical orders remain unchanged when products are modified
- Reduced coupling between modules
- Easier future module extraction

### Inventory Ownership

Inventory is owned exclusively by the `Catalog` module.

The Order module never modifies product stock directly.

Instead, inventory operations are executed through `CatalogInterface`, ensuring a single source of truth for stock management.

### Order Confirmation Workflow

Orders are created in the `Pending` state.

Product stock is not reduced during order creation.

When an order is confirmed:

1. Order requests inventory reduction through `CatalogInterface`
2. Catalog validates stock availability
3. Catalog reduces stock inside a database transaction
4. Order status changes to `Confirmed`

Benefits:

* Prevents inventory reservation for abandoned orders
* More closely resembles real-world e-commerce workflows
* Keeps inventory logic centralized

### Category Deletion

Products are not automatically deleted when a category is removed.

This allows products to be reassigned to another category rather than losing business data.

### Order Total

The order record contains a `total_amount` column.

Although the value can be calculated dynamically from order items, storing it provides:

- Faster reporting
- Simpler querying
- Historical consistency

---

## Technology Stack

### Backend

- PHP 8.4
- Laravel 13
- PostgreSQL
- Laravel Modules

### Frontend

- Livewire 4
- Blade
- Tailwind CSS

### Administration

- Filament 5

### Quality

- PHPStan (Larastan)
- Duster

### Testing

- Pest
- PHPUnit

---

## Installation

### Clone Repository

```bash
git clone <repository-url>
cd merchanto-test
```

### Install Dependencies

```bash
composer install
npm install
```

### Environment

```bash
cp .env.example .env
php artisan key:generate
```

Configure database credentials in `.env`.

### Database

```bash
php artisan migrate
php artisan module:migrate
```

### Create Filament User

```bash
php artisan make:filament-user
```

### Build Assets

```bash
npm run build
```

### Run Application

```bash
php artisan serve
```

---

## Admin Area

Available at:

```text
/admin
```

### Features

- Product Categories CRUD
- Products CRUD
- Orders CRUD
- Order Items Management

---

## Public Pages

### Product Catalog

```text
/catalogs
```

Features:

- Product browsing
- Pagination

### Order Creation

```text
/orders/create
```

Features:

- Product selection
- Quantity management
- Automatic total calculation

---

## Testing

### Run All Tests

```bash
php artisan test
```

### Run Individual Test Suites

```bash
php artisan test Modules/Catalog/tests
php artisan test Modules/Order/tests
```

### Implemented Tests

#### Catalog

- ProductManagementTest
- ProductDisplayTest

#### Order

- OrderCreationTest
- OrderManagementTest

---

## Static Analysis

### PHPStan

```bash
vendor/bin/phpstan analyse --memory-limit=1G
```

### Duster

```bash
vendor/bin/duster lint
```

### Auto-Fix

```bash
vendor/bin/duster fix
```

---

## CI/CD

GitHub Actions pipeline includes:

### Lint Workflow

- Duster
- PHPStan

### Test Workflow

- Dependency installation
- Asset build
- Automated tests

### Target Runtime

- PHP 8.4
