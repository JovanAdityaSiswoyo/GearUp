# Multi-Image Upload - Developer Quick Reference

## 📍 Find What You Need

### Forms (HTML/Blade)
```
resources/views/admin/products/create.blade.php    - Product create form
resources/views/admin/products/edit.blade.php      - Product edit form
resources/views/admin/packages/create.blade.php    - Package create form
resources/views/admin/packages/edit.blade.php      - Package edit form
```

### Models
```
app/Models/Product.php                             - Has images() relationship
app/Models/Package.php                             - Has images() relationship
app/Models/ProductImage.php                        - Gallery image model
app/Models/PackageImage.php                        - Gallery image model
```

### Controllers
```
app/Http/Controllers/ProductController.php         - store(), update(), destroy()
app/Http/Controllers/PackageController.php         - store(), update(), destroy()
app/Http/Controllers/GalleryImageController.php    - destroy() for AJAX
```

### Routes
```
routes/web.php                                     - DELETE /admin/gallery-images/{id}
```

### Database
```
database/migrations/2026_01_22_050221_create_product_images_table.php
database/migrations/2026_01_22_050222_create_package_images_table.php
```

---

## 🔧 Common Tasks

### Get All Gallery Images for a Product
```php
$product = Product::with('images')->find($id);
foreach ($product->images as $image) {
    echo $image->image; // file path
    echo $image->order; // sort order
}
```

### Get Ordered Images
```php
$images = $product->images()->orderBy('order')->get();
```

### Create Gallery Image Manually
```php
$product->images()->create([
    'image' => 'path/to/image.jpg',
    'order' => 0,
]);
```

### Delete Gallery Image Manually
```php
$image = ProductImage::find($id);
Storage::disk('public')->delete($image->image);
$image->delete();
```

### Get Image URL
```php
$image = ProductImage::find($id);
$url = asset('storage/' . $image->image);
```

---

## 📋 Validation Rules

### In Controller
```php
$validated = $request->validate([
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    'images' => 'nullable|array',
    'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
]);
```

### Accepted Formats
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- Maximum size: 2MB per image

---

## 🗂️ Storage Paths

### Files Stored In
```
storage/app/public/products/           - Product main images
storage/app/public/product-gallery/    - Product gallery images
storage/app/public/packages/           - Package main images
storage/app/public/package-gallery/    - Package gallery images
```

### Public Access
```
public/storage/products/               - Symlink to above
public/storage/product-gallery/        - Symlink to above
public/storage/packages/               - Symlink to above
public/storage/package-gallery/        - Symlink to above
```

### From Blade View
```blade
{{ asset('storage/' . $image->image) }}
<!-- Outputs: /storage/product-gallery/filename.jpg -->
```

---

## 🧠 Database Queries

### Count Images for Product
```php
$count = $product->images()->count();
```

### Get Images by Order
```php
$images = $product->images()->orderBy('order', 'asc')->get();
```

### Delete All Images
```php
$product->images()->delete();
// Also deletes files via cascade
```

### Find Image by ID
```php
$image = ProductImage::findOrFail($id);
$image->product_id;  // UUID of parent product
$image->image;       // File path
$image->order;       // Sort order
```

---

## 🔗 API Endpoints

### Delete Gallery Image (AJAX)
```javascript
fetch('/admin/gallery-images/123', {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
})
.then(r => r.json())
.then(data => console.log(data.message))
.catch(e => console.error(e));
```

### Response
```json
{
    "message": "Image deleted successfully"
}
```

---

## 🎨 Blade Template Snippets

### Display Gallery Grid
```blade
@if($product->images->count())
    <div class="grid grid-cols-4 gap-2">
        @foreach($product->images as $image)
            <img src="{{ asset('storage/' . $image->image) }}" 
                 alt="Gallery" class="w-full h-24 object-cover">
        @endforeach
    </div>
@endif
```

### Upload Form Input
```blade
<input type="file" name="images[]" accept="image/*" multiple>
```

### With Error Display
```blade
<input type="file" name="images[]" accept="image/*" multiple>
@error('images.*')
    <p class="text-red-500">{{ $message }}</p>
@enderror
```

---

## 🚨 Troubleshooting Commands

### Ensure Storage Symlink
```bash
php artisan storage:link
```

### Check Symlink Status
```bash
ls -l public/storage
# Should show: storage -> ../storage/app/public
```

### Clear Image Cache (if caching images)
```bash
php artisan cache:clear
```

### Check Database Tables
```bash
php artisan tinker
>>> Schema::getColumnListing('product_images')
>>> Schema::getColumnListing('package_images')
```

---

## 📊 Database Schema

### product_images
```
Column          Type            Notes
id              INT PRIMARY     Auto-increment
product_id      UUID FOREIGN    CASCADE DELETE
image           VARCHAR(255)    File path
order           INT DEFAULT 0   Sort order
created_at      TIMESTAMP       Auto
updated_at      TIMESTAMP       Auto
INDEX: product_id
```

### package_images
```
Column          Type            Notes
id              INT PRIMARY     Auto-increment
package_id      UUID FOREIGN    CASCADE DELETE
image           VARCHAR(255)    File path
order           INT DEFAULT 0   Sort order
created_at      TIMESTAMP       Auto
updated_at      TIMESTAMP       Auto
INDEX: package_id
```

---

## 🔒 Security Checklist

When modifying this feature:
- ✅ Always validate file type with `mimes:` rule
- ✅ Always validate file size with `max:` rule
- ✅ Always delete files from storage before deleting DB records
- ✅ Always require auth() check in routes
- ✅ Always include CSRF token in forms/AJAX
- ✅ Always sanitize file paths (never used unsanitized input in Storage)
- ✅ Always set proper file permissions on storage directory

---

## 📚 Related Documentation

- **MULTI_IMAGE_UPLOAD_IMPLEMENTATION.md** - Full technical spec
- **MULTI_IMAGE_QUICK_START.md** - User guide
- **IMPLEMENTATION_COMPLETE.md** - Complete specification
- **FEATURE_SUMMARY.md** - Visual overview
- **VERIFICATION_CHECKLIST.md** - Implementation verification

---

**Last Updated**: January 22, 2026
**Status**: Production Ready ✅
