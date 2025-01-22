<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Wyświetla listę ulubionych produktów użytkownika z opcjami filtrowania i sortowania.
     */
    public function index(Request $request)
    {
        $categories = Category::all();
    
        // Pobierz ulubione produkty użytkownika z filtrami
        $favorites = Favorite::where('user_id', Auth::id())
            ->whereHas('product', function ($query) use ($request) {
                // Filtrowanie po kategorii
                if ($request->filled('category')) {
                    $query->where('category_id', $request->category);
                }
    
                // Filtrowanie po wyszukiwaniu
                if ($request->filled('search')) {
                    $query->where(function ($q) use ($request) {
                        $q->where('title', 'like', '%' . $request->search . '%')
                          ->orWhere('artist', 'like', '%' . $request->search . '%');
                    });
                }
    
                // Filtrowanie po typie nośnika (format)
                if ($request->filled('format')) {
                    $query->where('format', $request->format);
                }
            });
    
        // Obsługa sortowania
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'title_asc':
                    $favorites = $favorites->join('products', 'favorites.product_id', '=', 'products.id')
                                           ->orderBy('products.title', 'asc');
                    break;
                case 'title_desc':
                    $favorites = $favorites->join('products', 'favorites.product_id', '=', 'products.id')
                                           ->orderBy('products.title', 'desc');
                    break;
                case 'price_asc':
                    $favorites = $favorites->join('products', 'favorites.product_id', '=', 'products.id')
                                           ->orderBy('products.price', 'asc');
                    break;
                case 'price_desc':
                    $favorites = $favorites->join('products', 'favorites.product_id', '=', 'products.id')
                                           ->orderBy('products.price', 'desc');
                    break;
            }
        }
    
        // Dodaj relację z produktami i paginację
        $favorites = $favorites->with('product.category')->paginate(12);
    
        // Jeśli żądanie jest AJAX-em, zwróć tylko widok listy ulubionych
        if ($request->ajax()) {
            return response()->json([
                'html' => view('favorites.partials.list', compact('favorites'))->render(),
            ]);
        }
    
        return view('favorites.index', compact('favorites', 'categories'));
    }
    

    /**
     * Dodaje produkt do ulubionych użytkownika.
     */
    public function store(Request $request, Product $product)
    {
        Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ]);

        return redirect()->back()->with('success', 'Product added to favorites!');
    }

    /**
     * Usuwa produkt z ulubionych użytkownika.
     */
    public function destroy(Product $product)
    {
        Favorite::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return redirect()->back()->with('success', 'Product removed from favorites.');
    }
}
