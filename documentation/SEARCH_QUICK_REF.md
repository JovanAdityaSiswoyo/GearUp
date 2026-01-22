# Search Feature - Quick Reference

## Usage

```bash
GET /api/{endpoint}?search={keyword}
```

## Examples

```bash
# Products
GET /api/products?search=tent
GET /api/products?search=camping&page=2

# Categories  
GET /api/categories?search=outdoor

# Packages
GET /api/packages?search=family

# Users (protected)
GET /api/users?search=john
Authorization: Bearer {token}

# Books (protected)
GET /api/books?search=BOOK-001
Authorization: Bearer {token}
```

## PowerShell

```powershell
# Public
Invoke-RestMethod -Uri "http://localhost:8000/api/products?search=tent"

# Protected
$headers = @{"Authorization" = "Bearer $token"}
Invoke-RestMethod -Uri "http://localhost:8000/api/users?search=john" -Headers $headers
```

## Searchable Fields

| Endpoint | Fields |
|----------|--------|
| products | name, desc, status |
| categories | categories |
| packages | name_package |
| users | name, email |
| admins | name, email |
| officers | name, email |
| books | book_code, booker_name, booker_email, status |
| book-products | book_code, booker_name, booker_email, status |
| user-info | phone |
| detail-books | participant_name, participant_email, participant_telp |
| detail-book-products | participant_name, participant_email, participant_telp |

## Features

✅ Case insensitive  
✅ Partial matching  
✅ Multiple fields (OR)  
✅ Works with pagination  
✅ Empty results safe  

## Full Documentation

See [SEARCH_DOCUMENTATION.md](SEARCH_DOCUMENTATION.md) for complete guide.
