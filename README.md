# Foxy Store 🦊 REST API

This project implements a simple REST API for managing nature products (e.g. fruits, vegetables, nuts), including product history tracking (price changes, stock, etc..). Simply everything what a fox in the forest needs to start an e-commerce store. 😄

The API is built using the [Nette](https://nette.org/) framework and uses a MariaDB database. The architecture is divided into logical *managers*, each representing a specific REST endpoint.

## ✨ Features

* Add new products and edit them
* Product list optional filtered by price, items in stock and name
* Product detail by ID
* Product history tracking
* Pagination support for lists
* OpenAPI 3.1 specification
* Dockerized development environment

## 🛠️ Technologies Used

* PHP 8.2
* Nette Framework
* MariaDB
* Docker
* Swagger UI
* OpenAPI 3.1

## 🚀 Running the Project (Docker)

1. Install packages with composer:

```bash
composer install
```

2. Copy `config/api.neon.docker` to `config/api.neon`
3. Start the application:

```bash
docker-compose up --build -d
```

4. The application will be available at: `http://localhost:8080/v1`
5. Swagger UI (OpenAPI docs): `http://localhost:8081`
6. PhpMyAdmin: `http://localhost:8090`

## 📘️ OpenAPI Documentation

The complete API specification is available in the [`openapi/openapi.yml`](openapi/openapi.yml) file.

* Swagger UI: `http://localhost:8081`
* Direct access: `http://localhost:8080/v1/openapi.yml` or `http://localhost:8080/v1/openapi.json`

> ⚠️ **Note: Due to CORS limitations and local Docker networking, only `GET` requests work correctly from Swagger UI. Write operations (`POST`, `PUT`, etc.) must be tested using tools like Postman or curl.**

## 🔄 API Endpoints

### `GET /product`

Returns a product detail by ID (e.g., `?id=123`).

### `POST /product`

Creates a new product.

### `PATCH /product`

Partially updates an existing product (e.g., `?id=123`).

### `PUT /product`

Upserts a product (insert or update, `?id=123` optional).

### `DELETE /product`

Soft deletes a product (e.g., `?id=123`).

### `GET /products`

Returns a list of products with support for filtering and pagination:

* `name` – partial name match (case-insensitive)
* `minPrice`, `maxPrice` – price filtering
* `minStock`, `maxStock` – stock filtering
* `limit`, `offset` – pagination control

### `GET /product/history`

Returns the history of a product by ID (e.g., `?id=123`) with support for pagination:

* `limit`, `offset` – pagination control

### `GET /product/history/price`

Returns the price history of a product by ID (e.g., `?id=123`) with support for pagination:

* `limit`, `offset` – pagination control

## 🧰 Project Structure

* `app/Models/Manager/*` – API logic (ProductManager, ProductListManager, ...)
* `app/Models/Repository/*` – DB layer
* `app/Models/Entity/*` – Data entities
* `app/Models/Validator/*` – Input validation
* `app/ApiModule/Presenters` – Endpoint entry points
* `config/*.neon` – Nette configuration
* `initdb/*` - Database initialization scripts
* `openapi/openapi.yml` – OpenAPI specification
* `docker-compose.yml` – Docker setup

## 🦊 Author

Created by **Ladislav Alexa**
