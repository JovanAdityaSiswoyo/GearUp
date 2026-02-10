<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\Book;
use App\Models\BookPackageProduct;
use App\Models\Package;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Atomic Assignment Service
 * 
 * Menghandle assignment unit fisik spesifik saat package booking dibuat
 * Mencegah double booking dan memastikan tracking unit yang akurat
 */
class AtomicAssignmentService
{
    /**
     * Assign units untuk package booking
     * 
     * @param Book $book
     * @return array Result dengan success/failure info
     */
    public function assignUnitsForPackage(Book $book): array
    {
        try {
            DB::beginTransaction();

            $package = $book->package;
            if (!$package) {
                throw new \Exception('Package not found');
            }

            $assignedUnits = [];
            $failures = [];

            // Get all products in package with qty from pivot
            $packageProducts = $package->products()->get();

            foreach ($packageProducts as $product) {
                $requiredQty = $product->pivot->qty ?? 1;

                // Find available units for this product
                $availableUnits = Unit::forProduct($product->id)
                    ->available()
                    ->limit($requiredQty)
                    ->lockForUpdate() // Prevent race condition
                    ->get();

                if ($availableUnits->count() < $requiredQty) {
                    $failures[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'required' => $requiredQty,
                        'available' => $availableUnits->count(),
                        'message' => "Insufficient units for {$product->name}. Required: {$requiredQty}, Available: {$availableUnits->count()}"
                    ];
                    continue;
                }

                // Assign each unit
                foreach ($availableUnits as $unit) {
                    // Create book_package_products record
                    $bookPackageProduct = BookPackageProduct::create([
                        'id' => \Illuminate\Support\Str::uuid(),
                        'id_book' => $book->id,
                        'id_product' => $product->id,
                        'id_unit' => $unit->id,
                        'qty' => 1, // Each record is 1 unit
                        'is_packed' => false,
                    ]);

                    // Lock the unit
                    $unit->lock();

                    $assignedUnits[] = [
                        'unit_id' => $unit->id,
                        'serial_number' => $unit->serial_number,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'book_package_product_id' => $bookPackageProduct->id,
                    ];

                    Log::info("Unit assigned", [
                        'booking_id' => $book->id,
                        'booking_code' => $book->booking_code,
                        'unit_id' => $unit->id,
                        'serial' => $unit->serial_number,
                        'product' => $product->name,
                    ]);
                }
            }

            if (!empty($failures)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Failed to assign units for some products',
                    'failures' => $failures,
                    'assigned' => [],
                ];
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'All units assigned successfully',
                'assigned' => $assignedUnits,
                'failures' => [],
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Atomic assignment failed', [
                'booking_id' => $book->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'System error during unit assignment: ' . $e->getMessage(),
                'assigned' => [],
                'failures' => [],
            ];
        }
    }

    /**
     * Release units saat booking dibatalkan
     * 
     * @param Book $book
     * @return bool
     */
    public function releaseUnitsForPackage(Book $book): bool
    {
        try {
            DB::beginTransaction();

            $bookPackageProducts = BookPackageProduct::where('id_book', $book->id)->get();

            foreach ($bookPackageProducts as $bpp) {
                if ($bpp->id_unit) {
                    $unit = Unit::find($bpp->id_unit);
                    if ($unit) {
                        $unit->release();
                        
                        Log::info("Unit released", [
                            'booking_id' => $book->id,
                            'unit_id' => $unit->id,
                            'serial' => $unit->serial_number,
                        ]);
                    }
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to release units', [
                'booking_id' => $book->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get packing list untuk officer
     * 
     * @param Book $book
     * @return array
     */
    public function getPackingList(Book $book): array
    {
        $bookPackageProducts = BookPackageProduct::where('id_book', $book->id)
            ->with(['product', 'unit'])
            ->get();

        $packingList = [];

        foreach ($bookPackageProducts as $bpp) {
            $packingList[] = [
                'id' => $bpp->id,
                'product_name' => $bpp->product->name ?? 'Unknown',
                'unit_serial' => $bpp->unit->serial_number ?? 'Not Assigned',
                'unit_id' => $bpp->unit->id ?? null,
                'is_packed' => $bpp->is_packed,
                'packed_at' => $bpp->packed_at,
                'packed_by' => $bpp->packed_by,
            ];
        }

        return $packingList;
    }

    /**
     * Mark item as packed (QR scan oleh Officer)
     * 
     * @param string $bookPackageProductId
     * @param string $officerId
     * @return bool
     */
    public function markAsPacked(string $bookPackageProductId, string $officerId): bool
    {
        try {
            $bpp = BookPackageProduct::find($bookPackageProductId);
            
            if (!$bpp) {
                return false;
            }

            $bpp->update([
                'is_packed' => true,
                'packed_at' => now(),
                'packed_by' => $officerId,
            ]);

            Log::info("Item packed", [
                'book_package_product_id' => $bookPackageProductId,
                'officer_id' => $officerId,
                'unit_serial' => $bpp->unit->serial_number ?? 'N/A',
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to mark as packed', [
                'book_package_product_id' => $bookPackageProductId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check apakah semua items sudah dipacking
     * 
     * @param Book $book
     * @return bool
     */
    public function isPackingComplete(Book $book): bool
    {
        $totalItems = BookPackageProduct::where('id_book', $book->id)->count();
        $packedItems = BookPackageProduct::where('id_book', $book->id)
            ->where('is_packed', true)
            ->count();

        return $totalItems > 0 && $totalItems === $packedItems;
    }
}
