# 📡 API Documentation: Packing & Route Map

## 📋 Table of Contents
1. [Officer Packing API](#officer-packing-api)
2. [Courier Route Map API](#courier-route-map-api)
3. [Error Handling](#error-handling)
4. [Authentication](#authentication)
5. [Examples](#examples)

---

## Officer Packing API

### Base URL
```
http://localhost:8000/officer/packing
```

### 1. List Bookings for Packing

**Endpoint:** `GET /officer/packing`

**Authentication:** Required (Officer)

**Description:** Get list of bookings ready for packing with search/filter support.

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `search` | string | No | Search by booking code, customer name, or email |
| `page` | integer | No | Page number (default: 1) |

**Response (200 OK):**
```json
{
  "bookings": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "booking_code": "BK-001",
      "booker_name": "Ahmad",
      "status": "CONFIRMED",
      "package": {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "name": "Paket Hemat",
        "price": 150000
      },
      "user": {
        "email": "ahmad@email.com",
        "phone": "081234567890"
      },
      "total_items": 3,
      "book_package_products": [
        {
          "id": "550e8400-e29b-41d4-a716-446655440002",
          "id_product": "550e8400-e29b-41d4-a716-446655440003",
          "product": {
            "name": "Tenda Consina"
          },
          "is_packed": false,
          "id_unit": null
        }
      ]
    }
  ],
  "pagination": {
    "total": 25,
    "count": 10,
    "per_page": 10,
    "current_page": 1,
    "last_page": 3
  }
}
```

**Example Request:**
```bash
curl -X GET "http://localhost:8000/officer/packing?search=BK-001" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

### 2. Get Packing Checklist

**Endpoint:** `GET /officer/packing/{booking_id}`

**Authentication:** Required (Officer)

**Parameters:**
| Parameter | Type | Location | Description |
|-----------|------|----------|-------------|
| `booking_id` | UUID | URL | Booking ID |

**Response (200 OK):**
```json
{
  "booking": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "booking_code": "BK-001",
    "booker_name": "Ahmad",
    "status": "CONFIRMED",
    "rental_start": "2026-02-10",
    "rental_end": "2026-02-15",
    "user": {
      "name": "Ahmad Suryanto",
      "email": "ahmad@email.com",
      "phone": "081234567890"
    },
    "package": {
      "id": "550e8400-e29b-41d4-a716-446655440001",
      "name": "Paket Hemat",
      "price": 150000
    }
  },
  "packingList": [
    {
      "book_package_product_id": "550e8400-e29b-41d4-a716-446655440002",
      "product_name": "Tenda Consina",
      "quantity": 1,
      "unit_serial": "TEN-005-WXYZ",
      "is_packed": false,
      "packed_at": null,
      "packed_by_name": null
    },
    {
      "book_package_product_id": "550e8400-e29b-41d4-a716-446655440003",
      "product_name": "Kompor Consina",
      "quantity": 1,
      "unit_serial": "KMP-012-QRST",
      "is_packed": false,
      "packed_at": null,
      "packed_by_name": null
    },
    {
      "book_package_product_id": "550e8400-e29b-41d4-a716-446655440004",
      "product_name": "Matras Quechua",
      "quantity": 1,
      "unit_serial": "MTR-099-UVWX",
      "is_packed": false,
      "packed_at": null,
      "packed_by_name": null
    }
  ],
  "packedCount": 0,
  "totalCount": 3,
  "isComplete": false
}
```

**Example Request:**
```bash
curl -X GET "http://localhost:8000/officer/packing/550e8400-e29b-41d4-a716-446655440000" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

### 3. Assign Units (Atomic)

**Endpoint:** `POST /officer/packing/{booking_id}/assign-units`

**Authentication:** Required (Officer)

**Parameters:**
| Parameter | Type | Location | Description |
|-----------|------|----------|-------------|
| `booking_id` | UUID | URL | Booking ID |

**Request Body:**
```json
{}
```

**Response (200 OK) - Success:**
```json
{
  "success": true,
  "message": "Units assigned successfully",
  "assigned": [
    {
      "product_id": "550e8400-e29b-41d4-a716-446655440005",
      "unit_id": "550e8400-e29b-41d4-a716-446655440006",
      "serial_number": "TEN-005-WXYZ"
    },
    {
      "product_id": "550e8400-e29b-41d4-a716-446655440007",
      "unit_id": "550e8400-e29b-41d4-a716-446655440008",
      "serial_number": "KMP-012-QRST"
    },
    {
      "product_id": "550e8400-e29b-41d4-a716-446655440009",
      "unit_id": "550e8400-e29b-41d4-a716-446655440010",
      "serial_number": "MTR-099-UVWX"
    }
  ],
  "failures": []
}
```

**Response (400 Bad Request) - Insufficient Units:**
```json
{
  "success": false,
  "message": "Insufficient units available",
  "assigned": [],
  "failures": [
    {
      "product": "Tenda Consina",
      "reason": "No available units"
    }
  ]
}
```

**Example Request:**
```bash
curl -X POST "http://localhost:8000/officer/packing/550e8400-e29b-41d4-a716-446655440000/assign-units" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{}'
```

---

### 4. Scan Unit (QR Verification)

**Endpoint:** `POST /officer/packing/scan-unit`

**Authentication:** Required (Officer)

**Request Body:**
```json
{
  "book_package_product_id": "550e8400-e29b-41d4-a716-446655440002",
  "unit_serial": "TEN-005-WXYZ"
}
```

**Response (200 OK) - Success:**
```json
{
  "success": true,
  "message": "✅ Unit marked as packed!",
  "packed_at": "09 Feb 2026 10:30",
  "data": {
    "book_package_product_id": "550e8400-e29b-41d4-a716-446655440002",
    "unit_serial": "TEN-005-WXYZ",
    "packed_at": "2026-02-09 10:30:45"
  }
}
```

**Response (422 Unprocessable Entity) - Serial Mismatch:**
```json
{
  "success": false,
  "message": "❌ Serial tidak sesuai! Expected: TEN-005-WXYZ, Got: KMP-012-QRST"
}
```

**Response (422 Unprocessable Entity) - Validation Error:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "book_package_product_id": ["The book_package_product_id field is required."],
    "unit_serial": ["The unit_serial field is required."]
  }
}
```

**Example Request:**
```bash
curl -X POST "http://localhost:8000/officer/packing/scan-unit" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "book_package_product_id": "550e8400-e29b-41d4-a716-446655440002",
    "unit_serial": "TEN-005-WXYZ"
  }'
```

---

### 5. Finalize Packing

**Endpoint:** `POST /officer/packing/{booking_id}/finalize`

**Authentication:** Required (Officer)

**Parameters:**
| Parameter | Type | Location | Description |
|-----------|------|----------|-------------|
| `booking_id` | UUID | URL | Booking ID |

**Request Body:**
```json
{}
```

**Response (200 OK) - Success:**
```json
{
  "success": true,
  "message": "Packing complete!",
  "redirect": "/officer/packing",
  "data": {
    "booking_id": "550e8400-e29b-41d4-a716-446655440000",
    "status": "READY_FOR_PICKUP",
    "packed_count": 3,
    "total_items": 3
  }
}
```

**Response (422 Unprocessable Entity) - Packing Not Complete:**
```json
{
  "success": false,
  "message": "Packing not complete. 2/3 items packed."
}
```

**Example Request:**
```bash
curl -X POST "http://localhost:8000/officer/packing/550e8400-e29b-41d4-a716-446655440000/finalize" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{}'
```

---

## Courier Route Map API

### Base URL
```
http://localhost:8000/courier/route-map
```

### 1. Route Map View

**Endpoint:** `GET /courier/route-map`

**Authentication:** Required (Courier)

**Description:** Get HTML view with interactive map.

**Response:** HTML page with embedded map

---

### 2. Route Map Data

**Endpoint:** `GET /courier/route-map/data`

**Authentication:** Required (Courier)

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `type` | string | No | Filter by task type: `all`, `delivery`, `return` |
| `priority` | string | No | Filter by priority: `all`, `high`, `normal` |

**Response (200 OK):**
```json
{
  "total_deliveries": 10,
  "total_returns": 5,
  "total_tasks": 15,
  "grouped_by_area": [
    {
      "address": "Jl. Sudirman No. 123, Jakarta",
      "count": 3,
      "latitude": -6.2088,
      "longitude": 106.8456,
      "has_delivery": true,
      "has_return": true,
      "tasks": [
        {
          "id": "550e8400-e29b-41d4-a716-446655440000",
          "booking_code": "BK-001",
          "type": "delivery",
          "booking_type": "package",
          "status": "READY_FOR_PICKUP",
          "customer_name": "Ahmad",
          "customer_phone": "081234567890",
          "address": "Jl. Sudirman No. 123, Jakarta",
          "item_name": "Paket Hemat",
          "priority": "normal"
        }
      ]
    }
  ],
  "all_tasks": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "booking_code": "BK-001",
      "type": "delivery",
      "booking_type": "package",
      "status": "READY_FOR_PICKUP",
      "customer_name": "Ahmad",
      "customer_phone": "081234567890",
      "address": "Jl. Sudirman No. 123, Jakarta",
      "item_name": "Paket Hemat",
      "priority": "normal",
      "latitude": -6.2088,
      "longitude": 106.8456
    }
  ]
}
```

**Example Request:**
```bash
curl -X GET "http://localhost:8000/courier/route-map/data?type=delivery&priority=high" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

## Error Handling

### HTTP Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| `200` | OK | Request successful |
| `400` | Bad Request | Insufficient units available |
| `401` | Unauthorized | Missing/invalid token |
| `403` | Forbidden | User doesn't have required role |
| `404` | Not Found | Booking not found |
| `422` | Unprocessable Entity | Validation error or serial mismatch |
| `500` | Server Error | Database transaction failed |

### Error Response Format

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Error for field"]
  }
}
```

---

## Authentication

### Headers Required
```
Authorization: Bearer {access_token}
Accept: application/json
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token} (for POST/PUT/DELETE)
```

### Getting Token
```bash
# Login
POST /login
{
  "email": "officer@example.com",
  "password": "password"
}

# Response includes token
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

---

## Examples

### Complete Packing Workflow (cURL)

```bash
# Step 1: Get bookings
curl -X GET "http://localhost:8000/officer/packing" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Step 2: View packing checklist
BOOKING_ID="550e8400-e29b-41d4-a716-446655440000"
curl -X GET "http://localhost:8000/officer/packing/$BOOKING_ID" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Step 3: Assign units
curl -X POST "http://localhost:8000/officer/packing/$BOOKING_ID/assign-units" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{}'

# Step 4: Scan first unit
curl -X POST "http://localhost:8000/officer/packing/scan-unit" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "book_package_product_id": "550e8400-e29b-41d4-a716-446655440002",
    "unit_serial": "TEN-005-WXYZ"
  }'

# Step 5: Scan second unit
curl -X POST "http://localhost:8000/officer/packing/scan-unit" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "book_package_product_id": "550e8400-e29b-41d4-a716-446655440003",
    "unit_serial": "KMP-012-QRST"
  }'

# Step 6: Scan third unit
curl -X POST "http://localhost:8000/officer/packing/scan-unit" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "book_package_product_id": "550e8400-e29b-41d4-a716-446655440004",
    "unit_serial": "MTR-099-UVWX"
  }'

# Step 7: Finalize packing
curl -X POST "http://localhost:8000/officer/packing/$BOOKING_ID/finalize" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{}'
```

### JavaScript (Fetch API)

```javascript
// Scan unit function
async function scanUnit(bookPackageProductId, unitSerial) {
  const response = await fetch('/officer/packing/scan-unit', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
      book_package_product_id: bookPackageProductId,
      unit_serial: unitSerial
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    alert('✅ Unit packed successfully!');
    // Update UI
  } else {
    alert('❌ ' + data.message);
  }
  
  return data;
}

// Usage
scanUnit('550e8400-e29b-41d4-a716-446655440002', 'TEN-005-WXYZ');
```

---

## Rate Limiting

Currently no rate limiting implemented. Contact admin for production deployment.

---

## Changelog

### v1.0 (February 9, 2026)
- Initial API documentation
- 5 packing endpoints
- 2 route map endpoints
- Complete error handling documentation

---

**Last Updated:** February 9, 2026
**API Version:** 1.0
**Status:** ✅ Ready for Integration
