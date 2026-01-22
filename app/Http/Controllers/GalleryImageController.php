<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use App\Models\PackageImage;
use Illuminate\Support\Facades\Storage;

class GalleryImageController extends Controller
{
    /**
     * Delete a gallery image
     */
    public function destroy($id)
    {
        // Try to find in ProductImage first
        $productImage = ProductImage::find($id);
        if ($productImage) {
            // Delete file from storage
            if (Storage::disk('public')->exists($productImage->image)) {
                Storage::disk('public')->delete($productImage->image);
            }
            $productImage->delete();
            return response()->json(['message' => 'Image deleted successfully'], 200);
        }

        // Try to find in PackageImage
        $packageImage = PackageImage::find($id);
        if ($packageImage) {
            // Delete file from storage
            if (Storage::disk('public')->exists($packageImage->image)) {
                Storage::disk('public')->delete($packageImage->image);
            }
            $packageImage->delete();
            return response()->json(['message' => 'Image deleted successfully'], 200);
        }

        return response()->json(['message' => 'Image not found'], 404);
    }
}
