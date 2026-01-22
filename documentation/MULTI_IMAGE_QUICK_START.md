# Multi-Image Upload Feature - Quick Start Guide

## What's New ✨

You can now upload **multiple images** (gallery) for both Products and Packages!

## How to Use

### 1. Creating a Product with Gallery Images

```
Admin Panel → Products → Create Product
```

**Form Fields:**
- Product Name (required)
- Category (required)
- Description
- Price per Day (required)
- Stock (required)
- **Product Image** - Main product thumbnail (optional)
- **Gallery Images (Multiple)** - Multiple photos for product gallery (optional)

**Example:**
1. Fill in product details
2. Upload main product image
3. Click "Select Files" for Gallery Images and choose 3-5 photos
4. Click "Create Product"

### 2. Editing a Product's Gallery

```
Admin Panel → Products → Edit Product
```

**View Gallery:**
- Current gallery images display in a 4-column grid
- Hover over any image to see the delete button

**Manage Images:**
- Click the red delete icon to remove an image
- Select additional images in the "Gallery Images" field to add more
- Click "Update Product"

### 3. Same for Packages

```
Admin Panel → Packages → Create/Edit Package
```

All functionality works identically for packages.

## Technical Details

### Image Storage
- Main images: `/storage/products/` and `/storage/packages/`
- Gallery images: `/storage/product-gallery/` and `/storage/package-gallery/`
- All stored in `public/storage/` for direct web access

### Validation
- Accepted formats: JPEG, PNG, JPG, GIF
- Maximum size: 2MB per image
- No limit on number of gallery images

### Automatic Cleanup
- Deleting a product/package automatically deletes all gallery images
- Deleting an image via the delete button removes it from storage

### Gallery Order
Images maintain their upload order using an "order" field in the database

## File Paths

### Backend Files
- Model: `app/Models/ProductImage.php`, `app/Models/PackageImage.php`
- Controller: `app/Http/Controllers/GalleryImageController.php`
- Views: `resources/views/admin/products/create.blade.php`, `edit.blade.php`
- Views: `resources/views/admin/packages/create.blade.php`, `edit.blade.php`
- Routes: `routes/web.php` (DELETE route for gallery images)

### Database Tables
- `product_images` - Stores product gallery image references
- `package_images` - Stores package gallery image references

## Troubleshooting

### Images Not Showing
1. Ensure storage symlink exists: `php artisan storage:link`
2. Check permissions on `storage/app/public/` folder

### Upload Failing
1. Verify file is an image (JPEG, PNG, JPG, GIF)
2. Ensure file is under 2MB
3. Check server disk space

### Delete Button Not Working
1. Ensure JavaScript is enabled in browser
2. Check browser console for errors (F12)
3. Verify CSRF token is properly set

## API/AJAX Endpoint

For programmatic deletion:
```
DELETE /admin/gallery-images/{imageId}
Headers: 
  - X-CSRF-TOKEN: [csrf-token]
  - Content-Type: application/json
  - Accept: application/json
```

Example response: `{"message": "Image deleted successfully"}`

## Next Steps

Consider these potential enhancements:
- [ ] Drag-and-drop image reordering
- [ ] Image cropping/editing before upload
- [ ] Batch image deletion
- [ ] Image compression on upload
- [ ] Image alt text/captions
- [ ] Lightbox gallery display on product detail pages

