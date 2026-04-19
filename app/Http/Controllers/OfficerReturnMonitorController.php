<?php
namespace App\Http\Controllers;

use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;
use App\Enums\ItemStatus;
use Illuminate\Http\Request;

class OfficerReturnMonitorController extends Controller
{
    public function index()
    {
        // Show all items in the return workflow, including completed records for history.
        $bookProducts = BookProduct::whereIn('order_status', [
                OrderStatus::DIPINJAM,
                OrderStatus::SELESAI,
            ])
            ->with(['user', 'product'])
            ->orderBy('checkout_appointment_end', 'asc')
            ->get();

        $books = Book::whereIn('order_status', [
                OrderStatus::DIPINJAM,
                OrderStatus::SELESAI,
            ])
            ->with(['user', 'package'])
            ->orderBy('checkout_appointment_end', 'asc')
            ->get();

        // Merge both collections
        $returns = $bookProducts->concat($books)->sortBy('checkout_appointment_end');

        // Paginate manually
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $returns->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $returns = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $returns->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('officer.returns-monitor', compact('returns'));
    }

    public function startReturn($id)
    {
        $return = $this->resolveReturn($id);

        if ($return->order_status !== OrderStatus::DIPINJAM || $return->item_status !== ItemStatus::DEPLOYED) {
            return redirect()->back()->with('error', 'Item belum siap memulai proses pengembalian.');
        }

        $return->item_status = ItemStatus::RETURNING;
        $return->return_started_at = now();
        $return->save();

        return redirect()->back()->with('success', 'Pengembalian berhasil dimulai.');
    }

    public function startInspection($id)
    {
        $return = $this->resolveReturn($id);

        if ($return->order_status !== OrderStatus::DIPINJAM || $return->item_status !== ItemStatus::RETURNING) {
            return redirect()->back()->with('error', 'Item harus dalam perjalanan kembali sebelum masuk inspeksi.');
        }

        $return->item_status = ItemStatus::IN_INSPECTION;
        $return->inspection_started_at = now();
        $return->save();

        return redirect()->back()->with('success', 'Item berhasil masuk tahap inspeksi.');
    }

    public function process($id)
    {
        $return = $this->resolveReturn($id);

        if ($return->order_status !== OrderStatus::DIPINJAM || $return->item_status !== ItemStatus::IN_INSPECTION) {
            return redirect()->back()->with('error', 'Item belum menunggu approval officer.');
        }

        $return->order_status = OrderStatus::SELESAI;
        $return->item_status = ItemStatus::AVAILABLE;
        $return->returned_at = now();
        $return->save();

        return redirect()->back()->with('success', 'Pengembalian berhasil diproses dan status pinjaman selesai.');
    }

    private function resolveReturn($id)
    {
        $return = BookProduct::find($id);

        if (!$return) {
            $return = Book::findOrFail($id);
        }

        return $return;
    }
}
