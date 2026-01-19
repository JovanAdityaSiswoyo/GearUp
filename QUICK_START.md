# Quick Start Guide - API

## Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+
- Server sudah running: `php artisan serve`
- Database sudah di-seed: `php artisan db:seed`

## 1. Test API dalam 30 detik

### Windows (PowerShell)
```powershell
.\test-api.ps1
```

### Linux/Mac (Bash)
```bash
chmod +x test-api.sh
./test-api.sh
```

## 2. Manual Testing

### Login dan Dapatkan Token
```bash
# PowerShell
$response = Invoke-RestMethod -Uri "http://localhost:8000/api/login" -Method Post -Body '{"email":"test@example.com","password":"password"}' -ContentType "application/json"
$token = $response.access_token
Write-Host "Token: $token"

# Bash/cURL
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}' \
  | jq -r '.access_token')
echo "Token: $TOKEN"
```

### Test Public Endpoint
```bash
# PowerShell
Invoke-RestMethod -Uri "http://localhost:8000/api/products"

# cURL
curl http://localhost:8000/api/products
```

### Test Protected Endpoint
```bash
# PowerShell
$headers = @{"Authorization" = "Bearer $token"}
Invoke-RestMethod -Uri "http://localhost:8000/api/users" -Headers $headers

# cURL
curl -H "Authorization: Bearer $TOKEN" http://localhost:8000/api/users
```

### Test Search Feature
```bash
# PowerShell - Search products
Invoke-RestMethod -Uri "http://localhost:8000/api/products?search=tent"

# PowerShell - Search users (protected)
Invoke-RestMethod -Uri "http://localhost:8000/api/users?search=john" -Headers $headers

# cURL - Search products
curl "http://localhost:8000/api/products?search=tent"

# cURL - Search with pagination
curl "http://localhost:8000/api/products?search=camping&page=2"
```

## 3. Import ke Postman

1. Buka Postman
2. File → Import
3. Pilih `postman_collection.json`
4. Collection "AplikasiPinjam API" akan muncul
5. Set environment variable:
   - `base_url` = `http://localhost:8000/api`
6. Run request "Login" untuk auto-save token
7. Semua protected endpoints otomatis menggunakan token

## 4. View All Routes

```bash
php artisan route:list --path=api
```

Output: 59 API routes

## 5. Common Endpoints

### Authentication
```http
POST /api/register     # Register new user
POST /api/login        # Login (get token)
POST /api/logout       # Logout (revoke token)
GET  /api/me          # Get authenticated user
```

### Public (No Auth)
```http
GET /api/categories    # List categories
GET /api/products      # List products
GET /api/packages      # List packages
```

### Protected (Need Token)
```http
GET    /api/users           # List users
POST   /api/users           # Create user
GET    /api/users/{id}      # Get user
PUT    /api/users/{id}      # Update user
DELETE /api/users/{id}      # Delete user

# Same pattern untuk: admins, officers, categories*, 
# products*, packages*, books, user-info, detail-books,
# book-products, detail-book-products

# * Write operations only (GET masih public)
```

## 6. Example Requests

### Register
```json
POST /api/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Create Product
```json
POST /api/products
Authorization: Bearer {token}

{
  "id_admins": "uuid-admin",
  "id_category": "uuid-category",
  "name": "Tenda 4 Orang",
  "desc": "Tenda camping berkualitas",
  "status": "available",
  "price": 150000
}
```

### Create Booking
```json
POST /api/books
Authorization: Bearer {token}

{
  "id_package": "uuid-package",
  "id_user": "uuid-user",
  "book_code": "BOOK-001",
  "book_date": "2026-02-15",
  "checkin_time": "2026-02-15 14:00:00",
  "checkout_time": "2026-02-17 12:00:00",
  "booker_name": "John Doe",
  "booker_email": "john@example.com",
  "booker_telp": "08123456789",
  "status": "pending"
}
```

## 7. Response Examples

### Success (200/201)
```json
{
  "id": "9d4f1234-5678-90ab-cdef-1234567890ab",
  "name": "Product Name",
  "price": 150000,
  "created_at": "2026-01-19T07:41:45.000000Z"
}
```

### Paginated List (200)
```json
{
  "current_page": 1,
  "data": [...],
  "per_page": 15,
  "total": 75
}
```

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

### Unauthenticated (401)
```json
{
  "message": "Unauthenticated."
}
```

## 8. Troubleshooting

### Token tidak bekerja?
1. Pastikan format: `Authorization: Bearer {token}`
2. Token valid (belum logout)
3. Server masih running

### 404 Not Found?
1. Cek URL path benar: `/api/...`
2. Lihat route list: `php artisan route:list --path=api`
3. Pastikan API routes enabled di `bootstrap/app.php`

### Validation error?
1. Cek required fields
2. Cek format UUID untuk foreign keys
3. Cek unique constraints (email, book_code)

## 9. Documentation Files

- `README.md` - Main documentation
- `API_ENDPOINTS.md` - Complete endpoint reference
- `API_IMPLEMENTATION.md` - Implementation details
- `postman_collection.json` - Postman collection
- `test-api.ps1` - PowerShell test script
- `test-api.sh` - Bash test script
- `QUICK_START.md` - This file

## 10. Next Steps

After testing API:
1. Build frontend (React/Vue/Mobile)
2. Implement API Resources untuk better JSON formatting
3. Add rate limiting
4. Add API documentation (Swagger/OpenAPI)
5. Deploy to production

---

**Happy coding! 🚀**
