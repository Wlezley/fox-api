# Foxy Store 🦊 REST API

This project implements a simple REST API for managing products (fruits, vegetables, nuts), including product history tracking. The API is built using the [Nette](https://nette.org/) framework and uses a MariaDB database. The architecture is divided into logical *managers*, each representing a specific REST endpoint.

## ✨ Features

* Product listing with filtering and pagination
* Product detail by ID
* Product history tracking
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

1. Copy `config/api.neon.docker` to `config/api.neon`
3. Start the application:

```bash
docker-compose up --build -d
```

3. The application will be available at: `http://localhost:8080/v1`
4. Swagger UI (OpenAPI docs): `http://localhost:8081`
5. PhpMyAdmin: `http://localhost:8090`

## 📘️ OpenAPI Documentation

The complete API specification is available in the [`openapi/openapi.yml`](openapi/openapi.yml) file.

* Swagger UI: `http://localhost:8081`
* Direct access: `http://localhost:8080/v1/openapi.yml` or `http://localhost:8080/v1/openapi.json`

> ⚠️ **Note: Due to CORS limitations and local Docker networking, only `GET` requests work correctly from Swagger UI. Write operations (`POST`, `PUT`, etc.) must be tested using tools like Postman or curl.**

## 🔄 API Endpoints

### `GET /v1/product`

Returns a product detail by ID (e.g., `?id=123`).

### `POST /v1/product`

Creates a new product.

### `PATCH /v1/product`

Partially updates an existing product (e.g., `?id=123`).

### `PUT /v1/product`

Upserts a product (insert or update, `?id=123` optional).

### `DELETE /v1/product`

Soft deletes a product (e.g., `?id=123`).

### `GET /v1/products`

Returns a list of products with support for filtering and pagination:

* `name` – partial name match (case-insensitive)
* `minPrice`, `maxPrice` – price filtering
* `minStock`, `maxStock` – stock filtering
* `limit`, `offset` – pagination control

### `GET /v1/product/history`

Returns the history of a product by ID (e.g., `?id=123`) with support for pagination:

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
