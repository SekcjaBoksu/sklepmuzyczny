<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Wyświetlanie listy produktów z kategoriami.
     */
    public function index(Request $request)
    {
        // Pobieranie wszystkich kategorii do widoku
        $categories = Category::all();

        // Obsługa filtrów
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('artist', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        // Sortowanie
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
            }
        }

        // Pobieranie produktów z paginacją
        $products = $query->with('category')->paginate(12);

        // Obsługa AJAX (filtrowanie dynamiczne)
        if ($request->ajax()) {
            return response()->json([
                'html' => view('products.partials.product-list', compact('products'))->render(),
            ]);
        }

        // Przekazywanie aktualnych filtrów do widoku
        $filters = $request->only(['search', 'category', 'format', 'sort']);

        return view('products.product-list', compact('products', 'categories', 'filters'));
    }

    /**
     * Filtrowanie i wyszukiwanie produktów z dynamicznym ładowaniem (AJAX).
     */
    public function filter(Request $request)
    {
        $query = Product::query();

        // Wyszukiwanie po tytule i wykonawcy
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('artist', 'like', "%$search%");
            });
        }

        // Filtry
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        // Sortowanie
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
            }
        }

        // Paginacja
        $products = $query->with('category')->paginate(12);

        // Odpowiedź na AJAX (dynamiczne ładowanie produktów)
        if ($request->ajax()) {
            return response()->json([
                'html' => view('products.partials.product-list', compact('products'))->render(),
            ]);
        }

        // Przekazywanie aktualnych filtrów do widoku
        $categories = Category::all();
        $filters = $request->only(['search', 'category', 'format', 'sort']);

        return view('products.product-list', compact('products', 'categories', 'filters'));
    }



    public function show($id)
    {
        $product = Product::with(['reviews.user'])->findOrFail($id);
    
        return view('products.show', compact('product'));
    }
    
    

}
