# AZ Task - Laravel API

A clean, well-structured Laravel API for product management with JWT authentication, caching, and comprehensive testing.

## Features

- 🔐 JWT Authentication
- 📦 Product CRUD operations
- 🖼️ Image upload and management
- ⚡ Redis caching with intelligent invalidation
- 🧪 Comprehensive test coverage
- 🏗️ Clean architecture with Repository pattern
- 📝 API documentation
- 🐳 Docker support

## Architecture

This project follows clean architecture principles with:

- **Controllers**: Handle HTTP requests and responses
- **Services**: Business logic layer
- **Repositories**: Data access layer with caching
- **Models**: Eloquent models with relationships
- **Interfaces**: Contract definitions for dependency injection
- **Exceptions**: Custom exception handling

## Prerequisites

- PHP 8.2+
- Composer
- MySQL/PostgreSQL
- Redis (for caching)
- Node.js (for frontend assets)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd az-task
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=az_task
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Configure Redis**
   ```env
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Generate JWT secret**
   ```bash
   php artisan jwt:secret
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## API Documentation

### Authentication

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### Products

#### List Products
```http
GET /api/products?page=1&per_page=6
```

#### Create Product
```http
POST /api/products
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "name": "Product Name",
    "description": "Product description",
    "price": 99.99,
    "stock": 100,
    "image": [file]
}
```

#### Get Product
```http
GET /api/products/{id}
```

#### Update Product
```http
PUT /api/products/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "name": "Updated Name",
    "description": "Updated description",
    "price": 149.99,
    "stock": 50,
    "image": [file] // optional
}
```

#### Delete Product
```http
DELETE /api/products/{id}
Authorization: Bearer {token}
```

## Testing

Run the test suite:
```bash
php artisan test
```

Run tests with coverage:
```bash
php artisan test --coverage
```

## Docker

Build and run with Docker:
```bash
docker-compose up -d
```

## Project Structure

```
app/
├── Classes/
│   └── ApiResponse.php          # Centralized API response handling
├── Exceptions/
│   ├── Handler.php              # Global exception handler
│   └── ProductNotFoundException.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── ProductController.php
│   │   └── Auth/
│   │       └── AuthController.php
│   ├── Middleware/
│   │   └── JwtMiddleware.php
│   └── Requests/
│       ├── Auth/
│       │   └── LoginRequest.php
│       └── ProductRequest.php
├── Interfaces/
│   └── ProductRepositoryInterface.php
├── Models/
│   ├── Product.php
│   └── User.php
├── Providers/
│   └── AppServiceProvider.php
├── Repositories/
│   └── ProductRepository.php
└── Services/
    ├── CacheService.php
    ├── ImageService.php
    └── ProductService.php
```

## Key Improvements Made

1. **Fixed Repository Pattern**: Added proper interface with method signatures
2. **Centralized Caching**: Created dedicated CacheService
3. **Image Management**: Centralized image upload/delete logic
4. **Error Handling**: Custom exceptions and global handler
5. **Eloquent Models**: Replaced raw DB queries with Eloquent
6. **Consistent Responses**: All endpoints use ApiResponse class
7. **Type Safety**: Added proper type hints and return types
8. **Documentation**: Comprehensive PHPDoc comments
9. **Testing**: Improved test coverage and structure

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

This project is licensed under the MIT License.
