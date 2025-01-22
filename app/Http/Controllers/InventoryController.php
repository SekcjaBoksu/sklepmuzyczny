<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
    
        // Filtrowanie po tytule lub artyście (wyszukiwanie)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('artist', 'like', '%' . $search . '%');
            });
        }
    
        // Filtrowanie po ID
        if ($request->filled('product_id')) {
            $query->where('id', $request->product_id);
        }
    
        // Filtrowanie po kategorii
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
    
        // Filtrowanie po formacie
        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }
    
        // Sortowanie
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    break;
            }
        }
    
        // Paginacja
        $products = $query->paginate(10);
        $categories = Category::all();
    
        return view('inventory.index', compact('products', 'categories'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'format' => 'required|in:CD,Vinyl,Special Edition',
            'category_id' => 'required|exists:categories,id',
        ]);

        Product::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Produkt dodany pomyślnie!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('inventory.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'format' => 'required|in:CD,Vinyl,Special Edition',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Produkt zaktualizowany pomyślnie!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('inventory.index')->with('success', 'Produkt usunięty pomyślnie!');
    }
}
