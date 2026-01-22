<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# GearUp

GearUp adalah sistem manajemen booking dan peminjaman alat camping berbasis Laravel dengan fitur multi-user (Admin, Officer, User).

## Tech Stack

- Laravel 11.x
- PHP 8.2+
- MySQL 8.0+
- Livewire 4.x
- Tailwind CSS 4.x
- Sweet Alert
- **Laravel Sanctum** - API authentication with token-based auth

## Features

✅ **Complete REST API** with 59 endpoints  
✅ **Authentication** - Register, Login, Logout with Bearer tokens  
✅ **UUID Primary Keys** - All tables use UUID instead of auto-increment  
✅ **Eloquent Relationships** - One-to-One, One-to-Many, Many-to-Many  
✅ **Request Validation** - All endpoints have proper validation  
✅ **Pagination** - List endpoints return paginated results  
✅ **Seeder & Factories** - Complete test data generation  
✅ **API Resources** - Properly formatted JSON responses  
✅ **Public & Protected Routes** - Granular access control

## Setup Instructions

### 1. Clone Repository

```bash
git clone <repository-url>
cd AplikasiPinjam
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=camping_loan_app
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Setup

Buat database terlebih dahulu, lalu jalankan migrasi dan seeder:

```bash
php artisan migrate:fresh
php artisan db:seed
```

### 5. Run Application

Untuk development, jalankan kedua perintah berikut di terminal terpisah:

```bash
# Terminal 1 - Backend Server
php artisan serve

# Terminal 2 - Frontend Assets (Vite HMR)
npm run dev
```

Atau gunakan concurrently:

```bash
npx concurrently "php artisan serve" "npm run dev"
```

Aplikasi dapat diakses di: `http://localhost:8000`

### 6. Default Credentials

Setelah seeding, Anda dapat login dengan:

**Test User:**
- Email: `test@example.com`
- Password: `password`

**Admin/Officer/User lainnya:**
- Email: `[sesuai data seeder]`
- Password: `password`

## Database Structure

### Tables

- **users** - User accounts dengan UUID
- **user_info** - Detail informasi user (phone, birthday)
- **admins** - Admin accounts
- **officers** - Officer accounts
- **categories** - Kategori produk
- **products** - Produk yang tersedia (milik admin, per category)
- **packages** - Paket bundling produk
- **package_products** - Junction table (many-to-many packages-products)
- **books** - Booking paket oleh user
- **detail_books** - Detail participant dalam booking
- **book_products** - Booking individual product
- **detail_book_products** - Detail participant dalam book_products

### Key Features

- ✅ UUID sebagai primary key untuk semua tabel
- ✅ Foreign key constraints dengan cascade
- ✅ Relationships: One-to-One, One-to-Many, Many-to-Many
- ✅ Factory & Seeder untuk test data lengkap
- ✅ Authentication ready (User, Admin, Officer)

## Development Commands

### Database

```bash
# Reset dan migrate ulang
php artisan migrate:fresh

# Seed test data
php artisan db:seed

# Reset + Seed sekaligus
php artisan migrate:fresh --seed
```

### Generate Code

```bash
# Create migration
php artisan make:migration create_table_name

# Create model
php artisan make:model ModelName

# Create controller
php artisan make:controller ControllerName

# Create Livewire component
php artisan make:livewire ComponentName

# Create seeder
php artisan make:seeder SeederName

# Create factory
php artisan make:factory FactoryName
```

### Assets

```bash
# Development (watch mode)
npm run dev

# Production build
npm run build
```

## Project Structure

```
AplikasiPinjam/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/        # API Controllers dengan CRUD lengkap
│   │   └── Resources/      # API Resources untuk JSON response
│   ├── Models/             # Eloquent models dengan UUID & relationships
│   └── Providers/
├── database/
│   ├── factories/          # Model factories untuk testing
│   ├── migrations/         # Database migrations dengan UUID
│   └── seeders/           # Database seeders
├── resources/
│   ├── css/               # Tailwind CSS
│   ├── js/                # JavaScript & Livewire
│   └── views/             # Blade templates
└── routes/
    ├── web.php            # Web routes
    ├── api.php            # API routes dengan Sanctum auth
    └── console.php        # Console commands
```

## API Documentation

Base URL: `http://localhost:8000/api`

### Authentication Endpoints

#### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

Response: 201 Created
{
  "user": {...},
  "access_token": "1|xxxxx",
  "token_type": "Bearer"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "password"
}

Response: 200 OK
{
  "user": {...},
  "access_token": "2|xxxxx",
  "token_type": "Bearer"
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Logged out successfully"
}
```

#### Get Authenticated User
```http
GET /api/me
Authorization: Bearer {token}

Response: 200 OK
{
  "id": "uuid",
  "name": "Test User",
  "email": "test@example.com",
  "user_info": {...}
}
```

### Public Endpoints (No Auth Required)

#### Categories
```http
GET /api/categories              # List all categories
GET /api/categories?search=tenda # Search categories
GET /api/categories/{id}         # Get single category
```

#### Products
```http
GET /api/products                # List all products
GET /api/products?search=tenda   # Search products by name/desc/status
GET /api/products/{id}           # Get single product
```

#### Packages
```http
GET /api/packages                # List all packages
GET /api/packages?search=family  # Search packages by name
GET /api/packages/{id}           # Get single package
```

### Search Functionality

**Semua list endpoints mendukung parameter search:**

```bash
# Search products
GET /api/products?search=tenda

# Search users
GET /api/users?search=john

# Search books
GET /api/books?search=BOOK-001

# Search dengan pagination
GET /api/products?search=camping&page=2
```

**Searchable fields:**
- Products: name, desc, status
- Categories: categories
- Packages: name_package
- Users/Admins/Officers: name, email
- Books/BookProducts: book_code, booker_name, booker_email, status
- DetailBooks/DetailBookProducts: participant_name, participant_email, participant_telp
- UserInfo: phone

### Protected Endpoints (Requires Authentication)

Semua endpoint di bawah memerlukan header:
```
Authorization: Bearer {your_access_token}
```

#### Users
```http
GET    /api/users              # List all users
POST   /api/users              # Create new user
GET    /api/users/{id}         # Get single user
PUT    /api/users/{id}         # Update user
DELETE /api/users/{id}         # Delete user
```

#### User Info
```http
GET    /api/user-info          # List all user info
POST   /api/user-info          # Create user info
GET    /api/user-info/{id}     # Get single user info
PUT    /api/user-info/{id}     # Update user info
DELETE /api/user-info/{id}     # Delete user info
```

#### Admins
```http
GET    /api/admins             # List all admins
POST   /api/admins             # Create new admin
GET    /api/admins/{id}        # Get single admin
PUT    /api/admins/{id}        # Update admin
DELETE /api/admins/{id}        # Delete admin
```

#### Officers
```http
GET    /api/officers           # List all officers
POST   /api/officers           # Create new officer
GET    /api/officers/{id}      # Get single officer
PUT    /api/officers/{id}      # Update officer
DELETE /api/officers/{id}      # Delete officer
```

#### Categories (Protected Operations)
```http
POST   /api/categories         # Create category
PUT    /api/categories/{id}    # Update category
DELETE /api/categories/{id}    # Delete category
```

#### Products (Protected Operations)
```http
POST   /api/products           # Create product
PUT    /api/products/{id}      # Update product
DELETE /api/products/{id}      # Delete product
```

#### Packages (Protected Operations)
```http
POST   /api/packages           # Create package
PUT    /api/packages/{id}      # Update package
DELETE /api/packages/{id}      # Delete package
```

#### Books
```http
GET    /api/books              # List all books
POST   /api/books              # Create new book
GET    /api/books/{id}         # Get single book
PUT    /api/books/{id}         # Update book
DELETE /api/books/{id}         # Delete book
```

#### Detail Books
```http
GET    /api/detail-books       # List all detail books
POST   /api/detail-books       # Create detail book
GET    /api/detail-books/{id}  # Get single detail book
PUT    /api/detail-books/{id}  # Update detail book
DELETE /api/detail-books/{id}  # Delete detail book
```

#### Book Products
```http
GET    /api/book-products           # List all book products
POST   /api/book-products           # Create book product
GET    /api/book-products/{id}      # Get single book product
PUT    /api/book-products/{id}      # Update book product
DELETE /api/book-products/{id}      # Delete book product
```

#### Detail Book Products
```http
GET    /api/detail-book-products       # List all detail book products
POST   /api/detail-book-products       # Create detail book product
GET    /api/detail-book-products/{id}  # Get single detail book product
PUT    /api/detail-book-products/{id}  # Update detail book product
DELETE /api/detail-book-products/{id}  # Delete detail book product
```

### Example API Requests

#### Create Product
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id_admins": "admin-uuid",
    "id_category": "category-uuid",
    "name": "Tenda Camping 4 Orang",
    "desc": "Tenda berkualitas untuk 4 orang",
    "status": "available",
    "price": 150000
  }'
```

#### Create Package with Products
```bash
curl -X POST http://localhost:8000/api/packages \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name_package": "Paket Family Camping",
    "publish_start": "2026-02-01",
    "publish_end": "2026-12-31",
    "product_ids": ["product-uuid-1", "product-uuid-2"]
  }'
```

#### Create Booking
```bash
curl -X POST http://localhost:8000/api/books \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id_package": "package-uuid",
    "id_user": "user-uuid",
    "book_code": "BOOK-001",
    "book_date": "2026-02-15",
    "checkin_time": "2026-02-15 14:00:00",
    "checkout_time": "2026-02-17 12:00:00",
    "booker_name": "John Doe",
    "booker_email": "john@example.com",
    "booker_telp": "08123456789",
    "status": "pending"
  }'
```

### Response Format

All API responses follow this format:

**Success (200/201):**
```json
{
  "id": "uuid",
  "field1": "value1",
  "field2": "value2",
  "relationships": {...}
}
```

**Paginated List:**
```json
{
  "current_page": 1,
  "data": [...],
  "first_page_url": "...",
  "from": 1,
  "last_page": 5,
  "per_page": 15,
  "to": 15,
  "total": 75
}
```

**Validation Error (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["Error message"]
  }
}
```

**Unauthenticated (401):**
```json
{
  "message": "Unauthenticated."
}
```

**Not Found (404):**
```json
{
  "message": "Model not found."
}
```

## Testing API

### Using PowerShell Test Script (Recommended)

```powershell
# Make sure server is running
php artisan serve

# Run test script in new terminal
.\test-api.ps1
```

Script ini akan:
- Register user baru
- Login dan mendapatkan token
- Test semua endpoint utama (public & protected)
- Verifikasi authentication works properly

### Using Postman

1. Import `postman_collection.json` ke Postman
2. Set environment variable `base_url` = `http://localhost:8000/api`
3. Run request "Login" untuk auto-save token ke variable `access_token`
4. Token akan otomatis dipakai untuk semua protected endpoints
5. Test CRUD operations dengan collection yang tersedia

### Using cURL (Bash)

```bash
# Run test script
chmod +x test-api.sh
./test-api.sh
```

### Manual Testing dengan cURL

```bash
# Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}' \
  | jq -r '.access_token')

# Get authenticated user
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer $TOKEN"

# List products
curl -X GET http://localhost:8000/api/products

# Create category (protected)
curl -X POST http://localhost:8000/api/categories \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"categories":"Tenda"}'
```

### Quick API Reference

Lihat file [`API_ENDPOINTS.md`](API_ENDPOINTS.md) untuk dokumentasi lengkap semua 59 endpoints.

**Ringkasan:**
- 7 Public endpoints (Categories, Products, Packages - read only + Auth)
- 52 Protected endpoints (Full CRUD untuk semua resources)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
