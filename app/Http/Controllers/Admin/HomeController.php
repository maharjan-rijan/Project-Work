<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $totalAdmins = User::where('role',2)->count();
        $totalProducts = Product::count();
        $totalUsers = User::where('role',1)->count();
        return view('admin.dashboard',[
            'totalProducts' => $totalProducts,
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins
        ]);

    }

    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
