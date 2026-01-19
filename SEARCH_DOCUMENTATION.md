# Search Functionality Documentation

## Overview

Semua list endpoints di API sekarang mendukung fitur search menggunakan query parameter `?search=keyword`.

## How to Use

### Basic Search
```bash
GET /api/{endpoint}?search={keyword}
```

### Search with Pagination
```bash
GET /api/{endpoint}?search={keyword}&page={page_number}
```

## Searchable Endpoints

### 1. Products
**Endpoint:** `GET /api/products?search={keyword}`

**Searchable Fields:**
- `name` - Product name
- `desc` - Product description  
- `status` - Product status

**Examples:**
```bash
# Search by name
GET /api/products?search=tenda

# Search by description
GET /api/products?search=camping

# Search by status
GET /api/products?search=available
```

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": "uuid",
      "name": "Tenda Camping 4 Orang",
      "desc": "Tenda berkualitas untuk camping",
      "status": "available",
      "price": 150000,
      "admin": {...},
      "category": {...}
    }
  ],
  "per_page": 15,
  "total": 5
}
```

---

### 2. Categories
**Endpoint:** `GET /api/categories?search={keyword}`

**Searchable Fields:**
- `categories` - Category name

**Examples:**
```bash
GET /api/categories?search=tenda
GET /api/categories?search=peralatan
```

---

### 3. Packages
**Endpoint:** `GET /api/packages?search={keyword}`

**Searchable Fields:**
- `name_package` - Package name

**Examples:**
```bash
GET /api/packages?search=family
GET /api/packages?search=camping
```

---

### 4. Users
**Endpoint:** `GET /api/users?search={keyword}` *(Protected)*

**Searchable Fields:**
- `name` - User name
- `email` - User email

**Examples:**
```bash
GET /api/users?search=john
GET /api/users?search=@example.com
```

---

### 5. Admins
**Endpoint:** `GET /api/admins?search={keyword}` *(Protected)*

**Searchable Fields:**
- `name` - Admin name
- `email` - Admin email

**Examples:**
```bash
GET /api/admins?search=admin
GET /api/admins?search=@admin.com
```

---

### 6. Officers
**Endpoint:** `GET /api/officers?search={keyword}` *(Protected)*

**Searchable Fields:**
- `name` - Officer name
- `email` - Officer email

**Examples:**
```bash
GET /api/officers?search=officer
GET /api/officers?search=john
```

---

### 7. Books
**Endpoint:** `GET /api/books?search={keyword}` *(Protected)*

**Searchable Fields:**
- `book_code` - Booking code
- `booker_name` - Booker name
- `booker_email` - Booker email
- `status` - Booking status

**Examples:**
```bash
GET /api/books?search=BOOK-001
GET /api/books?search=john
GET /api/books?search=pending
```

---

### 8. Book Products
**Endpoint:** `GET /api/book-products?search={keyword}` *(Protected)*

**Searchable Fields:**
- `book_code` - Booking code
- `booker_name` - Booker name
- `booker_email` - Booker email
- `status` - Booking status

**Examples:**
```bash
GET /api/book-products?search=BP-001
GET /api/book-products?search=john
GET /api/book-products?search=confirmed
```

---

### 9. User Info
**Endpoint:** `GET /api/user-info?search={keyword}` *(Protected)*

**Searchable Fields:**
- `phone` - Phone number

**Examples:**
```bash
GET /api/user-info?search=0812
GET /api/user-info?search=3456
```

---

### 10. Detail Books
**Endpoint:** `GET /api/detail-books?search={keyword}` *(Protected)*

**Searchable Fields:**
- `participant_name` - Participant name
- `participant_email` - Participant email
- `participant_telp` - Participant phone

**Examples:**
```bash
GET /api/detail-books?search=john
GET /api/detail-books?search=@gmail.com
GET /api/detail-books?search=0812
```

---

### 11. Detail Book Products
**Endpoint:** `GET /api/detail-book-products?search={keyword}` *(Protected)*

**Searchable Fields:**
- `participant_name` - Participant name
- `participant_email` - Participant email
- `participant_telp` - Participant phone

**Examples:**
```bash
GET /api/detail-book-products?search=jane
GET /api/detail-book-products?search=@yahoo.com
```

---

## Search Behavior

### Case Insensitive
Search is case-insensitive by default (MySQL `LIKE` operator):
```bash
GET /api/products?search=TENDA  # Same as ?search=tenda
```

### Partial Match
Search finds partial matches anywhere in the field:
```bash
GET /api/products?search=tent   # Matches "Tenda Camping"
GET /api/users?search=john      # Matches "john@example.com" and "John Doe"
```

### Multiple Fields
When endpoint searches multiple fields, it uses OR logic:
```bash
# Searches in name OR email
GET /api/users?search=john
# Returns users where:
#   - name contains "john" OR
#   - email contains "john"
```

### Empty Search
If search parameter is empty or not provided, returns all results:
```bash
GET /api/products           # All products
GET /api/products?search=   # All products
```

---

## Testing Search

### Using PowerShell
```powershell
# Login first
$response = Invoke-RestMethod -Uri "http://localhost:8000/api/login" -Method Post -Body '{"email":"test@example.com","password":"password"}' -ContentType "application/json"
$token = $response.access_token
$headers = @{"Authorization" = "Bearer $token"}

# Search products (public)
Invoke-RestMethod -Uri "http://localhost:8000/api/products?search=tent"

# Search users (protected)
Invoke-RestMethod -Uri "http://localhost:8000/api/users?search=john" -Headers $headers

# Search with pagination
Invoke-RestMethod -Uri "http://localhost:8000/api/products?search=camping&page=2"
```

### Using cURL
```bash
# Search products (public)
curl "http://localhost:8000/api/products?search=tent"

# Search users (protected)
curl -H "Authorization: Bearer $TOKEN" \
  "http://localhost:8000/api/users?search=john"

# Search with pagination
curl "http://localhost:8000/api/products?search=camping&page=2"
```

### Using Postman
1. Import `postman_collection.json`
2. Add query parameter `search` dengan nilai keyword
3. Contoh URL: `{{base_url}}/products?search=tent`
4. Untuk protected endpoints, pastikan token sudah set

---

## Response Format

### With Results
```json
{
  "current_page": 1,
  "data": [
    {
      "id": "uuid",
      "field1": "value1",
      ...
    }
  ],
  "first_page_url": "...",
  "from": 1,
  "last_page": 1,
  "per_page": 15,
  "to": 5,
  "total": 5
}
```

### No Results
```json
{
  "current_page": 1,
  "data": [],
  "per_page": 15,
  "total": 0
}
```

---

## Advanced Usage

### Combining Search with Pagination
```bash
# Page 1
GET /api/products?search=tent&page=1

# Page 2  
GET /api/products?search=tent&page=2
```

### URL Encoding
For special characters, use URL encoding:
```bash
# Space -> %20
GET /api/products?search=tenda%20camping

# @ symbol -> %40
GET /api/users?search=user%40example.com
```

### Search Best Practices

1. **Be specific**: More specific searches return fewer, more relevant results
   ```bash
   # Less specific - may return many results
   GET /api/products?search=tent
   
   # More specific - fewer, more relevant results
   GET /api/products?search=tenda%20camping%204%20orang
   ```

2. **Use relevant fields**: Know which fields are searchable per endpoint

3. **Handle empty results**: Always check if `data` array is empty

4. **Combine with pagination**: For large result sets, use pagination

---

## Error Handling

### Invalid Endpoint
```json
{
  "message": "Not Found"
}
```

### Unauthenticated (Protected Endpoints)
```json
{
  "message": "Unauthenticated."
}
```

### Server Error
```json
{
  "message": "Server Error",
  "exception": "..."
}
```

---

## Performance Notes

- Search uses `LIKE '%keyword%'` which scans entire field values
- For large datasets, consider adding database indexes on searchable columns
- Results are paginated (15 items per page) to optimize performance
- Relationships are eager loaded to reduce N+1 query problems

---

## Summary

✅ **11 endpoints** with search functionality  
✅ **Public endpoints**: Categories, Products, Packages  
✅ **Protected endpoints**: Users, Admins, Officers, Books, etc.  
✅ **Case-insensitive** search  
✅ **Partial matching** support  
✅ **Multiple fields** per endpoint  
✅ **Pagination** compatible  
✅ **URL encoding** supported  

**Total searchable fields: 30+**
