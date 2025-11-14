<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Category;  
use App\Models\Donat;     

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Hitung total data masing-masing
        $users = User::count();
        $categories = Category::count();
        $donats = Donat::count();

        // Simpan dalam array widget
        $widget = [
            'users' => $users,
            'categories' => $categories,
            'donats' => $donats,
        ];

        // Kirim ke view home.blade.php
        return view('home', compact('widget'));
    }
}
