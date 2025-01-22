<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;


class AdminController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }
    
        // Pobierz użytkowników z wyszukiwaniem i filtrowaniem
        $query = User::query();
    
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
    
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
    
        $users = $query->paginate(10);
    
        return view('admin.users.index', compact('users'));
    }
    

    public function updateRole(Request $request, User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        $request->validate([
            'role' => 'required|in:client,employee,admin',
        ]);

        // Aktualizacja roli użytkownika
        $user->role = $request->role;
        $user->save(); // Koniecznie użyj save()

        return redirect()->route('admin.users.index')->with('success', 'Role updated successfully!');
    }


    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied');
        }

        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    

}
