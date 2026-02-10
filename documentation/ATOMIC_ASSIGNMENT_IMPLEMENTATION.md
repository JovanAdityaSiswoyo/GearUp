# 📦 Atomic Assignment & Unit Tracking System

## 🎯 Overview

Sistem **Atomic Assignment** memastikan setiap item dalam package di-assign ke unit fisik spesifik (berdasarkan Serial Number). Ini mencegah double booking dan memungkinkan tracking akurat dari gudang hingga return.

---

## 🗄️ Database Structure

### Tabel `units`
```sql
- id (UUID) - Primary key
- id_product (UUID) - Foreign key ke products
- serial_number (String, Unique) - QR code / nomor seri unit
- status (Enum) - available, booked, deployed, returning, in_inspection, maintenance, lost_scrapped
- notes (Text, Nullable) - Catatan kondisi/maintenance
- last_maintenance_at (Timestamp, Nullable)
- timestamps
```

### Tabel `book_package_products` (Updated)
```sql
- id (UUID)
- id_book (UUID) - Relasi ke books table
- id_product (UUID) - Relasi ke products table
- id_unit (UUID, Nullable) - **NEW**: Unit fisik yang di-assign
- qty (Integer) - Selalu 1 per record
- is_packed (Boolean) - **NEW**: Flag packing checklist
- packed_at (Timestamp, Nullable) - **NEW**: Waktu packing
- packed_by (UUID, Nullable) - **NEW**: Officer yang melakukan packing
- timestamps
```

---

## ⚙️ Alur Kerja (Workflow)

### 1. User Membuat Booking Package

**User:**
```
1. Pilih Package (misal: Paket Hemat - berisi Tenda, Kompor, Matras)
2. Isi detail booking (tanggal, alamat, dll)
3. Submit booking
```

**System:**
- Booking tersimpan dengan status `AWAITING_VALIDATION`
- **Belum ada unit yang di-assign**

---

### 2. Officer Validasi & Atomic Assignment

**Officer:**
```
1. Buka halaman "Packing Management"
2. Pilih booking yang perlu diproses
3. Klik "Assign Units" → System otomatis mencari unit available
```

**System (AtomicAssignmentService::assignUnitsForPackage):**
```php
1. Ambil package products (contoh: 1 Tenda, 1 Kompor, 1 Matras)

2. Untuk setiap product:
   - Cari unit dengan status 'available'
   - Lock unit (ubah status jadi 'booked')
   - Simpan di book_package_products dengan id_unit spesifik

3. Jika ada product yang stok habis → ROLLBACK semua assignment

4. Success → Return daftar unit yang di-assign:
   [
     {unit_id: xxx, serial: "TEN-001-ABCD", product: "Tenda"},
     {unit_id: yyy, serial: "KMP-002-EFGH", product: "Kompor"},
     {unit_id: zzz, serial: "MTR-003-IJKL", product: "Matras"}
   ]
```

**Manfaat:**
- ✅ Tidak ada double booking (unit di-lock saat di-assign)
- ✅ Tracking akurat (tahu unit mana yang keluar untuk booking mana)
- ✅ Accountability (jika barang rusak, bisa trace siapa user terakhir)

---

### 3. Officer Packing dengan QR Scanner

**Officer:**
```
1. Buka detail booking → Muncul "Packing Checklist"

Tampilan Checklist:
┌─────────────────────────────────────────────┐
│ Order #BK-12345 (Paket Hemat)               │
├─────────────────────────────────────────────┤
│ [ ] Tenda - Scan QR: TEN-001-ABCD           │
│ [ ] Kompor - Scan QR: KMP-002-EFGH          │
│ [ ] Matras - Scan QR: MTR-003-IJKL          │
├─────────────────────────────────────────────┤
│ Progress: 0/3 (0%)                          │
└─────────────────────────────────────────────┘

2. Scan QR code unit TEN-001-ABCD → System verify:
   - Apakah serial number sesuai dengan yang di-assign?
   - Jika YA → Mark as packed ✅
   - Jika TIDAK → Alert: "Serial number salah!" ❌

3. Ulangi untuk semua item

4. Setelah semua item discan (100%) → Klik "Finalize Packing"
   → Status booking berubah ke READY_FOR_PICKUP
```

**System (OfficerPackingController::scanUnit):**
```php
1. Terima input: book_package_product_id + unit_serial

2. Validasi:
   - Cek apakah serial number sesuai dengan unit yang di-assign
   - Cek apakah sudah dipacking sebelumnya

3. Jika valid:
   - Update is_packed = true
   - Simpan packed_at = now()
   - Simpan packed_by = officer_id

4. Return success/error message
```

---

## 📊 Contoh Kasus Nyata

### Skenario 1: Booking Paket Hemat

**User "Ahmad" pesan Paket Hemat:**
- 1x Tenda Consina Alpine 2P
- 1x Kompor Consina Portable
- 1x Matras Quechua Comfort

**Officer "Budi" proses:**
1. Klik "Assign Units"
2. System pilih:
   - TEN-005-WXYZ (dari 10 unit tenda available)
   - KMP-012-QRST (dari 10 unit kompor available)
   - MTR-099-UVWX (dari 10 unit matras available)
3. Status unit berubah: `available` → `booked`

**Officer "Budi" packing:**
1. Ambil fisik Tenda TEN-005-WXYZ
2. Scan QR → ✅ "Unit TEN-005-WXYZ packed!"
3. Ambil fisik Kompor KMP-012-QRST
4. Scan QR → ✅ "Unit KMP-012-QRST packed!"
5. Ambil fisik Matras MTR-099-UVWX
6. Scan QR → ✅ "Unit MTR-099-UVWX packed!"
7. Klik "Finalize Packing"
8. Courier bisa pickup barang

**Saat Return (Ahmad kembalikan barang):**
- Courier scan TEN-005-WXYZ → System tahu ini unit yang dipinjam Ahmad
- Jika rusak → Log: "Unit TEN-005-WXYZ rusak, terakhir dipinjam oleh Ahmad (Booking BK-12345)"

---

### Skenario 2: Stok Habis (Failure Case)

**User "Dian" pesan Paket Premium:**
- 3x Tenda (hanya 2 available)
- 2x Sleeping Bag (10 available)

**Officer klik "Assign Units":**
```
❌ Failed to assign units:
- Tenda: Required 3, Available 2 (Insufficient!)
- Sleeping Bag: OK

Transaction ROLLBACK → Tidak ada unit yang di-assign
```

**Officer:**
- Beri tahu user untuk ganti paket atau tunggu tenda available
- Atau assign manual dengan jumlah lebih sedikit

---

## 🔐 Keamanan & Validasi

### Atomic Transaction
```php
DB::beginTransaction();
try {
    // Assign all units dengan lockForUpdate()
    // Jika gagal 1 saja → rollback semua
    DB::commit();
} catch (Exception $e) {
    DB::rollBack();
}
```

### QR Verification
```php
// Officer scan unit TEN-999-AAAA
// Tapi system expect TEN-005-WXYZ

if ($scannedSerial !== $expectedSerial) {
    return "❌ Serial number tidak sesuai!";
}
```

---

## 📈 Monitoring & Reporting

### Dashboard Officer
```
📦 Packing Queue
- 5 bookings menunggu packing
- 3 bookings in progress
- 12 bookings ready for pickup

⚠️ Alerts
- Product "Tenda Arc'teryx Beta" stok menipis (2 available)
- 3 units perlu maintenance
```

### Unit History
```
Unit TEN-005-WXYZ History:
2026-01-15: Deployed to Ahmad (BK-12345)
2026-01-20: Returned - Good condition
2026-01-25: Deployed to Siti (BK-12399)
2026-01-30: Returned - Minor damage (tear on zipper)
2026-02-01: Maintenance completed
2026-02-09: Available
```

---

## 🛠️ Implementasi Teknis

### File Structure
```
app/
├── Services/
│   └── AtomicAssignmentService.php   # Main business logic
├── Models/
│   ├── Unit.php                      # Unit tracking model
│   └── BookPackageProduct.php        # Updated dengan unit tracking
└── Http/Controllers/
    └── OfficerPackingController.php  # Packing checklist

database/
├── migrations/
│   ├── 2026_02_09_083032_create_units_table.php
│   └── 2026_02_09_083043_add_unit_tracking_to_book_package_products.php
└── seeders/
    └── UnitSeeder.php                # Sample units

resources/views/officer/packing/
├── index.blade.php                   # List bookings
└── show.blade.php                    # Packing checklist
```

### API Endpoints
```
GET  /officer/packing                      # List bookings
GET  /officer/packing/{booking}            # Packing checklist
POST /officer/packing/{booking}/assign-units  # Atomic assignment
POST /officer/packing/scan-unit            # Scan QR
POST /officer/packing/{booking}/finalize   # Complete packing
```

---

## ✅ Benefits

1. **Akurasi 100%**
   - Tidak ada "stok -1" atau angka stok yang salah
   - Setiap booking tahu unit mana yang keluar

2. **Accountability**
   - Bisa trace unit rusak ke user terakhir
   - Audit trail lengkap

3. **Efisiensi**
   - Officer tidak perlu manual cek stok
   - Packing checklist otomatis

4. **Mencegah Loss**
   - Jika ada selisih fisik vs database → langsung ketahuan
   - QR scan memastikan barang yang benar masuk tas courier

---

## 🚀 Next Steps

1. **✅ Migration & Seeding** - Run migrations, seed sample units
2. **✅ Atomic Assignment** - Test assignment logic
3. **🔄 Packing Views** - Build officer packing interface (IN PROGRESS)
4. **⏳ QR Scanner** - Integrate QR scanning (HTML5 camera / barcode scanner hardware)
5. **⏳ Dashboard** - Add packing stats to officer dashboard
6. **⏳ Maintenance Tracking** - Track unit maintenance history

---

## 📝 Notes

- **Serial Number Format:** `{PREFIX}-{NUMBER}-{RANDOM}`
  - Contoh: `TEN-001-ABCD`, `KMP-012-EFGH`
- **QR Code:** Generate dari serial number, bisa print stiker
- **Status Flow:** 
  - `available` → `booked` (saat assignment)
  - `booked` → `deployed` (saat courier pickup)
  - `deployed` → `returning` (saat courier jemput return)
  - `returning` → `in_inspection` (saat officer terima return)
  - `in_inspection` → `available` / `maintenance` (setelah cek kondisi)
