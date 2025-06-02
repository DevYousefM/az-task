# AZ Task

A Laravel-based web application with Docker containerization support.

## 🚀 Features

- Built with Laravel 12.0
- JWT Authentication
- Docker containerization
- MySQL database
- Nginx web server
- PHPMyAdmin for database management

## 🔧 Prerequisites

- Docker and Docker Compose
- PHP 8.2 or higher
- Composer
- Node.js and npm

## 🛠️ Installation

1. Clone the repository:
```bash
git clone https://github.com/DevYousefM/az-task
cd az-task
```

2. Copy the environment file:
```bash
cp .env.example .env
```

3. Build and start the Docker containers:
```bash
docker compose up -d --build

```

4. Install PHP dependencies:
```bash
docker compose exec app composer install
```

5. Generate application key:
```bash
docker compose exec -w /var/www app php artisan key:generate
```

6. Generate JWT Secret:
```bash
docker compose exec -w /var/www app php artisan jwt:secret
```

# Laravel Storage Setup and Database Seeding with Docker

Follow these steps to fix storage permissions and run migrations & seeders inside your Docker container:

```bash
# 1. Create products directory inside storage
docker compose exec -w /var/www app mkdir -p storage/app/public/products

# 2. Remove any existing broken symlink
docker compose exec -w /var/www app rm -f public/storage

# 3. Create the storage symbolic link
docker compose exec -w /var/www app php artisan storage:link

# 4. Set ownership to web server user (www-data)
docker compose exec -w /var/www app chown -R www-data:www-data storage
docker compose exec -w /var/www app chown -R www-data:www-data public/storage

# 5. Set directory permissions
docker compose exec -w /var/www app chmod -R 775 storage
docker compose exec -w /var/www app chmod -R 775 public/storage
```
6. Migrate database with seeder:
```bash
docker compose exec -w /var/www app php artisan migrate --seed
```

## 🌐 Access Points

- **Application**: http://localhost:8050

## 📡 API Endpoints

### Authentication Endpoints

#### Login
- **URL**: `/api/auth/login`
- **Method**: `POST`
- **Auth Required**: No
- **Description**: Authenticate user and get JWT token
- **Request Body**:
  ```json
  {
    "email": "string",
    "password": "string"
  }
  ```
- **Success Response**: Returns JWT token
  ```json
  {
    "success": true,
    "data": {
      "token": "string"
    }
  }
  ```

#### Logout
- **URL**: `/api/auth/logout`
- **Method**: `POST`
- **Auth Required**: Yes (JWT)
- **Headers**:
  ```
  Authorization: Bearer <token>
  ```
- **Description**: Invalidate JWT token and logout user
- **Success Response**:
  ```json
  {
    "success": true,
    "message": "Successfully logged out"
  }
  ```

### Product Endpoints

#### List Products
- **URL**: `/api/products`
- **Method**: `GET`
- **Auth Required**: No
- **Description**: Get paginated list of all products
- **Query Parameters**:
  - `page`: Page number (optional)
  - `per_page`: Items per page (optional)
- **Success Response**:
  ```json
  {
    "success": true,
    "data": {
      "current_page": 1,
      "data": [
        {
          "id": "integer",
          "name": "string",
          "description": "string",
          "price": "decimal",
          "stock": "integer",
          "image": "string",
          "created_at": "timestamp",
          "updated_at": "timestamp"
        }
      ],
      "total": "integer",
      "per_page": "integer"
    }
  }
  ```

#### Get Single Product
- **URL**: `/api/products/{id}`
- **Method**: `GET`
- **Auth Required**: No
- **Description**: Get details of a specific product
- **Success Response**:
  ```json
  {
    "success": true,
    "data": {
      "id": "integer",
      "name": "string",
      "description": "string",
      "price": "decimal",
      "stock": "integer",
      "image": "string",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  }
  ```
- **Error Response** (404):
  ```json
  {
    "success": false,
    "message": "Product not found"
  }
  ```

#### Create Product
- **URL**: `/api/products`
- **Method**: `POST`
- **Auth Required**: Yes (JWT)
- **Headers**:
  ```
  Authorization: Bearer <token>
  Content-Type: multipart/form-data
  ```
- **Request Body**:
  ```
  name: string (required, max:255)
  description: string (optional)
  price: decimal (required, min:0)
  stock: integer (required, min:0)
  image: file (required, max:2MB, formats: jpg,png,jpeg)
  ```
- **Success Response**:
  ```json
  {
    "success": true,
    "data": {
      "id": "integer",
      "name": "string",
      "description": "string",
      "price": "decimal",
      "stock": "integer",
      "image": "string",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  }
  ```

#### Update Product
- **URL**: `/api/products/{id}`
- **Method**: `PUT`
- **Auth Required**: Yes (JWT)
- **Headers**:
  ```
  Authorization: Bearer <token>
  Content-Type: multipart/form-data
  ```
- **Request Body**: Same as Create Product
- **Success Response**: Same as Create Product
- **Error Response** (404):
  ```json
  {
    "success": false,
    "message": "Product not found"
  }
  ```

#### Delete Product
- **URL**: `/api/products/{id}`
- **Method**: `DELETE`
- **Auth Required**: Yes (JWT)
- **Headers**:
  ```
  Authorization: Bearer <token>
  ```
- **Success Response**:
  ```json
  {
    "success": true,
    "message": "Product deleted successfully"
  }
  ```

## 🐳 Docker Services

The application runs with the following services:
- **app**: PHP-FPM container running the Laravel application
- **nginx**: Web server
- **mysql**: Database server

## 🧪 Testing

The project includes a comprehensive test suite for both the API endpoints and authentication system. The tests are written using PHPUnit and Laravel's testing facilities.

### Running Tests

To run all tests:
```bash
docker compose exec -w /var/www app php artisan test
```

### Test Coverage

The test suite covers:

#### Authentication Tests (`tests/Feature/AuthTest.php`)
- Login with valid credentials
- Login with invalid credentials
- Logout with valid token
- Logout without token
- Login validation

#### Product API Tests (`tests/Feature/ProductTest.php`)
- List products (pagination)
- Show single product
- Create product with image upload
- Update existing product
- Delete product
- Validation rules
- Authentication requirements
