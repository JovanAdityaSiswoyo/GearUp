<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;

class PackageController extends Controller
{
    public function show($id)
    {
        $package = Package::with(['products.category', 'products.brand'])->findOrFail($id);
        return view('user.package.show', compact('package'));
    }
}
