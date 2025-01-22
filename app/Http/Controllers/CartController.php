<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = array_reduce($cart, function ($carry, $item) {
            return $carry + $item['price'] * $item['quantity'];
        }, 0);

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
    
        if (isset($cart[$id])) {
            if ($cart[$id]['quantity'] + 1 > $product->stock) {
                return redirect()->back()
                    ->with('error', "Nie można dodać więcej sztuk {$product->title}. Dostępne {$product->stock} sztuk.")
                    ->withInput();
            }
            $cart[$id]['quantity']++;
        } else {
            if ($product->stock < 1) {
                return redirect()->back()
                    ->with('error', "{$product->title} jest niedostępny, przepraszamy.")
                    ->withInput();
            }
            $cart[$id] = [
                'name' => $product->title,
                'price' => $product->price,
                'quantity' => 1,
            ];
        }
    
        session()->put('cart', $cart);
    
        // Pobieramy query string z aktualnego żądania
        $queryString = $request->getQueryString();
    
        // Dodajemy query string do przekierowania
        return redirect()->route('products.index', $queryString)
            ->with('success', "{$product->title} został dodany do koszyka.");
    }
    
    

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $product = Product::findOrFail($id);

        if ($request->quantity > $product->stock) {
            return redirect()->route('cart.index')
                ->with('error', "Cannot update quantity for {$product->title}. Only {$product->stock} left.");
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = max(1, $request->quantity);
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Koszyk zaktualizowany.');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Produkt usunięty z koszyka.');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Koszyk został wyczyszczony.');
    }
}
