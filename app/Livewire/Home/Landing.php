<?php

namespace App\Livewire\Home;

use Livewire\Component;
use App\Models\Product;
use App\Models\Package;

class Landing extends Component
{
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
