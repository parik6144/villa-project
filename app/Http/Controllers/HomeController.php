<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application's main page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (Auth::check()) {

            // Если email не подтвержден — на подтверждение
            if (!Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
            
            // Если пользователь имеет любую из перечисленных ролей — отправляем на панель Filament
            if (Auth::user()->hasAnyRole(['admin', 'agent', 'property_owner', 'manager', 'accountant'])) {
                return redirect()->to(filament()->getUrl());
            }

            // Для всех остальных авторизованных пользователей — домашняя страница
            return redirect()->intended(route('home', absolute: false));
        }

        // Для гостей (не авторизованных) показываем главную страницу
        return view('welcome');
    }
}
