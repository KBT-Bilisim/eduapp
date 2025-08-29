<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Eğer kullanıcı giriş yapmışsa dashboard'a yönlendir
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        // Giriş yapmamışsa login sayfasına yönlendir
        return redirect()->route('login');
    }
}
