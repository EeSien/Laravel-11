# Product Inventory Management API

## Setup

```bash
# 1. Install dependencies
composer install --no-security-blocking

# 2. Copy environment file and generate key
cp .env.example .env
php artisan key:generate

#3. Run migrations and seed demo data
php artisan migrate --seed

# 4. Start the development server
php artisan serve
```

The API is now available at `http://localhost:8000/api`.

---

## Authentication

All endpoints except `/register` and `/login` require a Bearer token.

### Endpoint

Can refer to the [Hoopscotch](hoppscotch-team-collections.json)

## Endpoints

All routes below require `Authorization: Bearer <token>`.

### Categories

| Method | URI                    | Description         |
| ------ | ---------------------- | ------------------- |
| GET    | `/api/categories`      | List all categories |
| POST   | `/api/categories`      | Create a category   |
| GET    | `/api/categories/{id}` | Get a category      |
| PUT    | `/api/categories/{id}` | Update a category   |
| DELETE | `/api/categories/{id}` | Delete a category   |

### Suppliers

| Method | URI                   | Description        |
| ------ | --------------------- | ------------------ |
| GET    | `/api/suppliers`      | List all suppliers |
| POST   | `/api/suppliers`      | Create a supplier  |
| GET    | `/api/suppliers/{id}` | Get a supplier     |
| PUT    | `/api/suppliers/{id}` | Update a supplier  |
| DELETE | `/api/suppliers/{id}` | Delete a supplier  |

### Products

| Method | URI                          | Description                                  |
| ------ | ---------------------------- | -------------------------------------------- |
| GET    | `/api/products`              | List active products (paginated, filterable) |
| POST   | `/api/products`              | Create a product                             |
| GET    | `/api/products/{id}`         | Get a product                                |
| PUT    | `/api/products/{id}`         | Update a product                             |
| DELETE | `/api/products/{id}`         | Soft-delete a product                        |
| POST   | `/api/products/{id}/restore` | Restore a soft-deleted product               |

#### Product Filters (query parameters)

| Parameter     | Type    | Description                    |
| ------------- | ------- | ------------------------------ |
| `category_id` | integer | Filter by category             |
| `price_min`   | numeric | Minimum price                  |
| `price_max`   | numeric | Maximum price                  |
| `stock_max`   | integer | Maximum stock quantity         |
| `low_stock`   | boolean | Only products with stock ≤ 10  |
| `per_page`    | integer | Results per page (default: 15) |

Example:

```
GET /api/products?category_id=2&price_min=10&price_max=100&low_stock=1&per_page=5
```

#### Create / Update Product Payload

```json
{
    "category_id": 1,
    "name": "Widget Pro",
    "sku": "WP-0001",
    "description": "Optional description",
    "price": 49.99,
    "stock_quantity": 100,
    "is_active": 1, // 1 or 0
    "supplier_ids[]": 1, //
    "supplier_ids[]": 2 //
}
```

`supplier_ids` is optional; when provided on update it **replaces** (syncs) the supplier list.

SKUs are automatically uppercased on save.

---

## Response Format

All responses use JSON API–style `data` wrapping via Laravel API Resources using CamelCase.

```json
{
    "data": {
        "id": 1,
        "name": "Widget Pro",
        "sku": "WP-0001",
        "price": "49.99",
        "formattedPrice": "$49.99",
        "stockQuantity": 100,
        "isActive": true,
        "category": { "id": 1, "name": "Electronics", "slug": "electronics" },
        "suppliers": [{ "id": 1, "name": "Acme Corp", "costPrice": "22.50" }],
        "createdAt": "2026-06-13T10:00:00+00:00",
        "updatedAt": "2026-06-13T10:00:00+00:00"
    }
}
```

Paginated lists include a `meta` and `links` key with pagination info.

---

## Running Tests

```bash
php artisan test
```
