<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Wyświetla formularz logowania.
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Widok formularza logowania
    }

    /**
     * Obsługuje logowanie użytkownika.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/products');
        }

        // Ustawienie błędu w sesji, jeśli logowanie się nie powiodło
        return back()->with('error', 'Nieprawidłowy e-mail lub hasło.');
    }
}
