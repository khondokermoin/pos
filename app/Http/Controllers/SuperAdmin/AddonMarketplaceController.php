<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Addon;

class AddonMarketplaceController extends Controller
{
    public function index()
    {
        // মার্কেটপ্লেসে সেই addon গুলো দেখাবে যেগুলো এখনো "installed" না
        $addons = Addon::where('is_installed', false)
            ->orWhereNull('is_installed')
            ->get();

        return view('super-admin.addons.marketplace', compact('addons'));
    }
}