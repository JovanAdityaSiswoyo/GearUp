# ⚡ Quick Reference Card

## 🚀 URLs

| Feature | URL | Role | Status |
|---------|-----|------|--------|
| Courier Map | `/courier/route-map` | Courier | ✅ Live |
| Officer Packing | `/officer/packing` | Officer | ✅ Live |
| Packing Detail | `/officer/packing/{id}` | Officer | ✅ Live |

---

## 📦 Database Tables

### units
```
id (UUID) | id_product | serial_number | status | notes | last_maintenance_at | timestamps
```
**Statuses:** available, booked, deployed, returning, in_inspection, maintenance, lost_scrapped

### book_package_products (Updated)
```
id_unit | is_packed | packed_at | packed_by | ... (existing fields)
```

---

## 🎮 API Endpoints

### GET /officer/packing
List bookings dengan search/filter
```json
Response: {
  "bookings": [...],
  "pagination": {...}
}
```

### GET /officer/packing/{booking_id}
Packing checklist detail
```json
Response: {
  "booking": {...},
  "packingList": [...],
  "packedCount": 2,
  "totalCount": 3,
  "isComplete": false
}
```

### POST /officer/packing/{booking_id}/assign-units
Auto-assign units (atomic)
```json
Request: {}
Response: {
  "success": true,
  "assigned": [{...}],
  "failures": [...]
}
```

### POST /officer/packing/scan-unit
Scan QR verification
```json
Request: {
  "book_package_product_id": "uuid",
  "unit_serial": "TEN-005-WXYZ"
}
Response: {
  "success": true,
  "message": "✅ Unit marked as packed!",
  "packed_at": "09 Feb 2026 10:30"
}
```

### POST /officer/packing/{booking_id}/finalize
Complete packing
```json
Request: {}
Response: {
  "success": true,
  "message": "Packing complete!",
  "redirect": "/officer/packing"
}
```

---

## 🛠️ Key Files

### Core Logic
- `app/Services/AtomicAssignmentService.php` - Business logic
- `app/Http/Controllers/OfficerPackingController.php` - Controller
- `app/Models/Unit.php` - Unit model
- `app/Models/BookPackageProduct.php` - Updated model

### Database
- `database/migrations/2026_02_09_083032_create_units_table.php`
- `database/migrations/2026_02_09_083043_add_unit_tracking_to_book_package_products.php`
- `database/seeders/UnitSeeder.php` - 538 units seeded

### Views
- `resources/views/officer/packing/index.blade.php` - List bookings
- `resources/views/officer/packing/show.blade.php` - Packing checklist
- `resources/views/courier/route-map.blade.php` - Courier map

### Routes
```php
// In routes/web.php
GET  /officer/packing
GET  /officer/packing/{booking}
POST /officer/packing/{booking}/assign-units
POST /officer/packing/scan-unit
POST /officer/packing/{booking}/finalize
GET  /courier/route-map
GET  /courier/route-map/data
```

---

## 💾 Common Commands

```bash
# Run migrations
php artisan migrate

# Seed units
php artisan db:seed --class=UnitSeeder

# Tinker console
php artisan tinker

# Test specific feature
php artisan test tests/Feature/OfficerPackingTest.php

# View logs
tail -f storage/logs/laravel.log

# Start dev server
php artisan serve
npm run dev
```

---

## 🔍 Quick Queries

```php
// Get available units for product
Unit::available()->forProduct($productId)->get()

// Check unit status
Unit::where('serial_number', 'TEN-005-WXYZ')->first()

// Get booking with assignments
Book::with('bookPackageProducts.unit')->find($id)

// Get packing progress
$booking->bookPackageProducts()
  ->selectRaw('COUNT(*) as total, SUM(is_packed) as packed')
  ->first()

// Get units needing maintenance
Unit::where('last_maintenance_at', '<', now()->subMonths(3))->get()
```

---

## 🧪 Test Data

**Seeded:** 538 units across all products
**Format:** `{PREFIX}-{NUMBER}-{RANDOM}`
- TEN-001-ABCD (Tenda)
- KMP-012-EFGH (Kompor)
- MTR-099-IJKL (Matras)

---

## ⚙️ Configuration

### Environment Variables
```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
DB_DATABASE=aplikasi_pinjam
```

### Permissions Required
```
Officer: manage-packing, scan-units
Courier: view-deliveries, view-returns
```

---

## ✅ Checklist

**Feature Complete?**
- ✅ Database: Units table created & seeded (538 units)
- ✅ Models: Unit, BookPackageProduct updated
- ✅ Service: AtomicAssignmentService implemented
- ✅ Controller: OfficerPackingController implemented
- ✅ Views: index.blade.php, show.blade.php
- ✅ Routes: All 5 routes configured
- ✅ Navigation: Officer sidebar updated (5 pages)
- ✅ Documentation: Complete

**Ready for Testing?**
- ✅ Backend: Complete
- ✅ Frontend: Complete
- ✅ Routes: Configured
- ⏳ Sample Bookings: Needed for testing

---

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| View not found | Check view file exists: `/resources/views/officer/packing/` |
| Serial number mismatch | Verify unit is assigned to booking |
| Atomic transaction failed | Check available units exist |
| Map doesn't load | Check Leaflet.js CDN loaded |
| Permission denied | Check officer role assigned |

---

## 📊 Testing Flow

1. **Create booking** → Status: AWAITING_VALIDATION
2. **Officer opens packing** → /officer/packing
3. **Officer clicks packing** → /officer/packing/{id}
4. **System auto-assigns units** → POST /assign-units
5. **Officer scans QR** → POST /scan-unit
6. **Check progress updates** → Visual progress bar
7. **Finalize packing** → POST /finalize
8. **Booking status** → READY_FOR_PICKUP

---

## 💡 Pro Tips

- Use `lockForUpdate()` when updating shared resources
- Always use atomic transactions for multi-step operations
- Verify serial numbers match before marking as packed
- Log all critical operations for audit trail
- Test with concurrent requests to verify locking

---

## 📞 Support

**Questions?** Check:
1. [IMPLEMENTATION_COMPLETE_SUMMARY.md](IMPLEMENTATION_COMPLETE_SUMMARY.md)
2. [TECHNICAL_DEVELOPER_GUIDE.md](TECHNICAL_DEVELOPER_GUIDE.md)
3. Code comments in `AtomicAssignmentService.php`

---

**Last Updated:** February 9, 2026
**Quick Access:** 30 second reads
