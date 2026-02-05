<?php

namespace App\Livewire\Home;

use Livewire\Component;
use App\Models\Product;
use App\Models\Package;
use Carbon\Carbon;

class Landing extends Component
{
    public $tanggal_ambil;
    public $durasi;
    public $tanggal_pengembalian;
    public $search = '';

    public function mount()
    {
        $this->tanggal_ambil = Carbon::today()->format('Y-m-d');
    }

    public function updatedTanggalAmbil()
    {
        $this->calculateDuration();
    }

    public function updatedTanggalPengembalian()
    {
        $this->calculateDuration();
    }

    private function calculateDuration()
    {
        if ($this->tanggal_ambil && $this->tanggal_pengembalian) {
            $start = Carbon::parse($this->tanggal_ambil);
            $end = Carbon::parse($this->tanggal_pengembalian);
            
            // Hitung selisih hari
            $this->durasi = $start->diffInDays($end);
            
            // Jika tanggal pengembalian lebih awal dari tanggal ambil, set 0
            if ($end->lt($start)) {
                $this->durasi = 0;
            }
        } else {
            $this->durasi = null;
        }
    }

    public function render()
    {
        return view('livewire.home.landing', [
            'bestPicks' => Product::with('category', 'brand')
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->take(12)
                ->get(),
            'packages' => Package::orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
        ]);
    }
}
