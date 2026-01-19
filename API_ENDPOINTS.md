# API Endpoints Summary

## Base URL
```
http://localhost:8000/api
```

## Authentication
All protected endpoints require Bearer token:
```
Authorization: Bearer {your_access_token}
```

## Search Functionality
All list endpoints support search via query parameter:
```
GET /api/products?search=tenda
GET /api/users?search=john
GET /api/books?search=BOOK-001
```

**Searchable fields by endpoint:**
- **Products**: name, desc, status
- **Categories**: categories
- **Packages**: name_package
- **Users**: name, email
- **Admins**: name, email
- **Officers**: name, email
- **Books**: book_code, booker_name, booker_email, status
- **Book Products**: book_code, booker_name, booker_email, status
- **User Info**: phone
- **Detail Books**: full_name, phone_number, emergency_phone_number, instagram_handle, other_socials
- **Detail Book Products**: full_name, phone_number, emergency_phone_number, instagram_handle, other_socials

---

## 🔓 Public Endpoints (No Authentication)

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/register` | Register new user |
| POST | `/login` | Login and get access token |

### Categories (Read Only)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/categories` | List all categories |
| GET | `/categories/{id}` | Get single category |

### Products (Read Only)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/products` | List all products |
| GET | `/products/{id}` | Get single product |

### Packages (Read Only)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/packages` | List all packages |
| GET | `/packages/{id}` | Get single package |

---

## 🔒 Protected Endpoints (Requires Authentication)

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/logout` | Logout (revoke token) |
| GET | `/me` | Get authenticated user |

### Users
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/users` | List all users |
| POST | `/users` | Create new user |
| GET | `/users/{id}` | Get single user |
| PUT | `/users/{id}` | Update user |
| DELETE | `/users/{id}` | Delete user |

### User Info
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/user-info` | List all user info |
| POST | `/user-info` | Create user info |
| GET | `/user-info/{id}` | Get single user info |
| PUT | `/user-info/{id}` | Update user info |
| DELETE | `/user-info/{id}` | Delete user info |

### Admins
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admins` | List all admins |
| POST | `/admins` | Create new admin |
| GET | `/admins/{id}` | Get single admin |
| PUT | `/admins/{id}` | Update admin |
| DELETE | `/admins/{id}` | Delete admin |

### Officers
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/officers` | List all officers |
| POST | `/officers` | Create new officer |
| GET | `/officers/{id}` | Get single officer |
| PUT | `/officers/{id}` | Update officer |
| DELETE | `/officers/{id}` | Delete officer |

### Categories (Write Operations)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/categories` | Create category |
| PUT | `/categories/{id}` | Update category |
| DELETE | `/categories/{id}` | Delete category |

### Products (Write Operations)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/products` | Create product |
| PUT | `/products/{id}` | Update product |
| DELETE | `/products/{id}` | Delete product |

### Packages (Write Operations)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/packages` | Create package |
| PUT | `/packages/{id}` | Update package |
| DELETE | `/packages/{id}` | Delete package |

### Books
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/books` | List all books |
| POST | `/books` | Create new book |
| GET | `/books/{id}` | Get single book |
| PUT | `/books/{id}` | Update book |
| DELETE | `/books/{id}` | Delete book |

### Detail Books
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/detail-books` | List all detail books |
| POST | `/detail-books` | Create detail book |
| GET | `/detail-books/{id}` | Get single detail book |
| PUT | `/detail-books/{id}` | Update detail book |
| DELETE | `/detail-books/{id}` | Delete detail book |

### Book Products
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/book-products` | List all book products |
| POST | `/book-products` | Create book product |
| GET | `/book-products/{id}` | Get single book product |
| PUT | `/book-products/{id}` | Update book product |
| DELETE | `/book-products/{id}` | Delete book product |

### Detail Book Products
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/detail-book-products` | List all detail book products |
| POST | `/detail-book-products` | Create detail book product |
| GET | `/detail-book-products/{id}` | Get single detail book product |
| PUT | `/detail-book-products/{id}` | Update detail book product |
| DELETE | `/detail-book-products/{id}` | Delete detail book product |

### Payments
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/payments` | List all payments (filter: ?status=paid&payable_type=book&payable_id=xxx) |
| POST | `/payments` | Create new payment |
| GET | `/payments/{id}` | Get single payment |
| PUT | `/payments/{id}` | Update payment |
| DELETE | `/payments/{id}` | Delete payment |

---

## Quick Start

### 1. Login and Get Token
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

### 2. Use Token in Requests
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 3. Test with PowerShell
```powershell
.\test-api.ps1
```

### 4. Import Postman Collection
Import `postman_collection.json` ke Postman untuk testing lengkap.

---

## Response Formats

### Success Response (Single Resource)
```json
{
  "id": "uuid-here",
  "field1": "value1",
  "field2": "value2"
}
```

### Success Response (List - Paginated)
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

### Error Response (Validation)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["Error message"]
  }
}
```

### Error Response (Unauthenticated)
```json
{
  "message": "Unauthenticated."
}
```

### Error Response (Not Found)
```json
{
  "message": "Model not found."
}
```

---

## Total Endpoints: 64

- **Public:** 7 endpoints
- **Protected:** 57 endpoints
