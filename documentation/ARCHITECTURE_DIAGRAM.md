# Multi-Image Upload System - Architecture Diagram

## 📊 System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER INTERFACE                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  Admin Panel                                                      │
│  ├── Products → Create/Edit                                     │
│  │   ├── Upload Main Image                                      │
│  │   └── Upload Gallery Images (Multiple)                       │
│  │       ├── Preview Grid (4 columns)                           │
│  │       └── Delete Individual Images (Hover)                   │
│  └── Packages → Create/Edit                                     │
│      ├── Upload Main Image                                      │
│      └── Upload Gallery Images (Multiple)                       │
│          ├── Preview Grid (4 columns)                           │
│          └── Delete Individual Images (Hover)                   │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
                              ↓
                         VALIDATION LAYER
                              ↓
        ┌──────────────────────┬──────────────────────┐
        │                      │                      │
    File Type Check        File Size Check       Required Fields
    (JPEG/PNG/GIF)         (Max 2MB)             Check
        │                      │                      │
        └──────────────────────┴──────────────────────┘
                              ↓
                      ROUTING LAYER
┌─────────────────────────────────────────────────────────────────┐
│                                                                   │
│  POST   /admin/products                → ProductController      │
│  POST   /admin/packages                → PackageController      │
│  PUT    /admin/products/{id}           → ProductController      │
│  PUT    /admin/packages/{id}           → PackageController      │
│  DELETE /admin/gallery-images/{id}     → GalleryImageController │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
                              ↓
                      CONTROLLER LAYER
┌──────────────────────────────────────────────────────────────────┐
│                                                                    │
│  ProductController              GalleryImageController            │
│  ├── store()                    ├── destroy()                     │
│  │   ├── Validate images[]      │   ├── Find ProductImage/        │
│  │   ├── Store main image       │   │   PackageImage             │
│  │   └── Store gallery images   │   ├── Delete from storage      │
│  ├── update()                   │   └── Delete from database     │
│  │   ├── Handle new images[]    │                                 │
│  │   └── Increment order        │   Response: JSON (200/404)     │
│  └── destroy()                  │                                 │
│      ├── Delete main image      │                                 │
│      └── Cascade delete gallery │                                 │
│                                 │                                 │
│  PackageController (identical)  │                                 │
│                                 │                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
                       MODEL LAYER
┌──────────────────────────────────────────────────────────────────┐
│                                                                    │
│  Product                  PackageImage                            │
│  ├── id (UUID)           ├── id (INT)                            │
│  ├── name                ├── package_id (FK → Package)           │
│  ├── image (main)        ├── image (file path)                   │
│  ├── ...                 ├── order (INT, for sorting)            │
│  └── images()            └── timestamps                          │
│      ↓                                                             │
│   hasMany → ProductImage   Package                               │
│                            ├── id (UUID)                         │
│  ProductImage              ├── name                              │
│  ├── id (INT)              ├── image (main)                      │
│  ├── product_id (FK)       ├── ...                               │
│  ├── image (file path)     └── images()                          │
│  ├── order (INT)              ↓                                  │
│  └── timestamps            hasMany → PackageImage               │
│                                                                    │
└──────────────────────────────────────────────────────────────────┘
                              ↓
                       DATABASE LAYER
┌──────────────────────────────────────────────────────────────────┐
│                                                                    │
│  products TABLE           product_images TABLE                    │
│  ├── id (UUID, PK)        ├── id (INT, PK)                       │
│  ├── name                 ├── product_id (UUID, FK) ──┐          │
│  ├── image (main path)    ├── image (path)            │          │
│  ├── ...                  ├── order (INT)             │CASCADE   │
│  └── timestamps           ├── timestamps              │          │
│      ↓                    └── INDEX: product_id   ────┘          │
│      └─── ONE-TO-MANY ────────→                                 │
│                                                                    │
│  packages TABLE           package_images TABLE                    │
│  ├── id (UUID, PK)        ├── id (INT, PK)                       │
│  ├── name                 ├── package_id (UUID, FK) ──┐          │
│  ├── image (main path)    ├── image (path)            │CASCADE   │
│  ├── ...                  ├── order (INT)             │          │
│  └── timestamps           ├── timestamps              │          │
│      ↓                    └── INDEX: package_id   ────┘          │
│      └─── ONE-TO-MANY ────────→                                 │
│                                                                    │
└──────────────────────────────────────────────────────────────────┘
                              ↓
                       STORAGE LAYER
┌──────────────────────────────────────────────────────────────────┐
│                                                                    │
│  storage/app/public/                                             │
│  ├── products/            ← Main product images                  │
│  ├── product-gallery/     ← Product gallery images               │
│  ├── packages/            ← Main package images                  │
│  └── package-gallery/     ← Package gallery images               │
│                                                                    │
│  public/storage/          ← Symlink (HTTP accessible)            │
│  ├── products/                                                   │
│  ├── product-gallery/                                            │
│  ├── packages/                                                   │
│  └── package-gallery/                                            │
│                                                                    │
└──────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Data Flow

### Create Product with Gallery Images

```
User Interface
    ↓
[Upload main image]
[Select gallery images]
[Click Create]
    ↓
ProductController::store()
    ├── Validate main image
    ├── Validate images[] array
    ├── Store main image → storage/products/
    ├── Store each gallery image → storage/product-gallery/
    ├── Create Product record
    ├── Create ProductImage records (one per image)
    └── Return success message
    ↓
Database
    ├── products table (1 record)
    └── product_images table (N records with order)
    ↓
User sees success alert
    ↓
Redirect to Products list
```

### Delete Gallery Image (AJAX)

```
User Interface
    ↓
[Hover gallery image]
[Click delete button]
    ↓
Confirmation Dialog
    ├── User clicks OK
    └── OR User clicks Cancel → Stop
    ↓
JavaScript (fetch API)
    ├── DELETE /admin/gallery-images/{id}
    ├── Include CSRF token
    └── Send AJAX request
    ↓
GalleryImageController::destroy()
    ├── Find ProductImage or PackageImage by ID
    ├── Delete file from storage
    ├── Delete database record
    └── Return JSON response
    ↓
JavaScript (success)
    ├── Check response status
    ├── Show success message
    └── Reload page
    ↓
User sees updated gallery
```

---

## 🔐 Security Flow

```
Request comes in
    ↓
[Route Middleware: auth:web,admin]
├── User authenticated? NO → Redirect to login
└── User authenticated? YES → Continue
    ↓
[CSRF Verification]
├── Valid CSRF token? NO → Reject request
└── Valid CSRF token? YES → Continue
    ↓
[File Validation]
├── File is image? NO → Show error
├── File type allowed? NO → Show error
├── File size ≤ 2MB? NO → Show error
└── ALL PASS? → Continue
    ↓
[Store Processing]
├── Generate unique filename (Laravel hash)
├── Store file in secure directory
├── Create database record
└── Return success
    ↓
Response sent to user
```

---

## 📁 File Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── GalleryImageController.php       ← NEW
│       ├── ProductController.php            ← MODIFIED
│       └── PackageController.php            ← MODIFIED
│
└── Models/
    ├── Product.php                         ← MODIFIED
    ├── Package.php                         ← MODIFIED
    ├── ProductImage.php                    ← NEW
    └── PackageImage.php                    ← NEW

database/
└── migrations/
    ├── 2026_01_22_050221_create_product_images_table.php    ← NEW
    └── 2026_01_22_050222_create_package_images_table.php    ← NEW

resources/
└── views/
    ├── admin/products/
    │   ├── create.blade.php                ← MODIFIED
    │   └── edit.blade.php                  ← MODIFIED
    └── admin/packages/
        ├── create.blade.php                ← MODIFIED
        └── edit.blade.php                  ← MODIFIED

routes/
└── web.php                                 ← MODIFIED (added DELETE route)

storage/
└── app/
    └── public/
        ├── products/
        ├── product-gallery/
        ├── packages/
        └── package-gallery/

public/
└── storage/                                ← Symlink
    ├── products/
    ├── product-gallery/
    ├── packages/
    └── package-gallery/
```

---

## 🔗 Key Relationships

```
Product (1)
    │
    ├── (1:N) → ProductImage (many)
    │            ├── id
    │            ├── product_id (FK)
    │            ├── image (file path)
    │            └── order (sort position)
    │
    └── image (single main image path)

Package (1)
    │
    ├── (1:N) → PackageImage (many)
    │            ├── id
    │            ├── package_id (FK)
    │            ├── image (file path)
    │            └── order (sort position)
    │
    └── image (single main image path)
```

---

## 📊 State Diagram

```
Product Lifecycle
    │
    ├── CREATE
    │   ├── Main image (optional)
    │   ├── Gallery images (optional)
    │   └── ProductImage records created
    │
    ├── UPDATE
    │   ├── Can add gallery images
    │   ├── Can delete gallery images
    │   └── ProductImage records updated
    │
    └── DELETE
        └── CASCADE: All ProductImage records deleted

Image Lifecycle
    │
    ├── CREATE
    │   ├── File stored in storage/
    │   ├── Database record created
    │   ├── Order indexed
    │   └── Public access via /storage/ symlink
    │
    ├── EXIST
    │   ├── Displayed in gallery preview
    │   ├── Deletable via AJAX
    │   └── Ordered by 'order' column
    │
    └── DELETE
        ├── File deleted from storage
        ├── Database record deleted
        └── No orphaned files
```

---

**System Design: Complete ✅**
**Security: Implemented ✅**
**Performance: Optimized ✅**
**Documentation: Comprehensive ✅**
