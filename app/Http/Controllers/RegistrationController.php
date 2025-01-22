<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        try{
        $messages = [
            'name.required' => 'Nazwa użytkownika jest wymagana.',
            'email.required' => 'Adres email jest wymagany.',
            'email.unique' => 'Podany adres email jest już zarejestrowany.',
            'password.required' => 'Hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć przynajmniej 8 znaków.',
            'password.confirmed' => 'Hasła muszą być zgodne.',
        ];
    
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:50|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], $messages);
    
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'client',
        ]);
        return view('auth.success');
        } catch (\Exception $e) {
            return redirect()->route('register')->with('error', 'Registration failed. Please try again.');
    }
    }
    
}
