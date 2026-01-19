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
│   ├── Http/Controllers/
│   ├── Models/          # Eloquent models dengan UUID & relationships
│   └── Providers/
├── database/
│   ├── factories/       # Model factories untuk testing
│   ├── migrations/      # Database migrations dengan UUID
│   └── seeders/        # Database seeders
├── resources/
│   ├── css/            # Tailwind CSS
│   ├── js/             # JavaScript & Livewire
│   └── views/          # Blade templates
└── routes/
    ├── web.php         # Web routes
    └── console.php     # Console commands
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
