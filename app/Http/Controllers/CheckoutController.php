<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Pokaż formularz checkout.
     */
    public function create()
    {
        $cart = session()->get('cart', []);
        $total = array_reduce($cart, function ($carry, $item) {
            return $carry + $item['price'] * $item['quantity'];
        }, 0);

        return view('checkout.create', compact('cart', 'total'));
    }

    /**
     * Obsłuż składanie zamówienia.
     */
    public function store(Request $request)
    {
        $request->validate([
            'address_line1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Twój koszyk jest pusty.');
        }

        DB::beginTransaction();

        try {
            // Zapisz adres
            $address = Address::create([
                'user_id' => auth()->id(),
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
            ]);

            // Oblicz sumę zamówienia
            $totalPrice = array_reduce($cart, function ($carry, $item) {
                return $carry + $item['price'] * $item['quantity'];
            }, 0);

            // Zapisz zamówienie
            $order = Order::create([
                'user_id' => auth()->id(),
                'address_id' => $address->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            // Przetwarzanie pozycji zamówienia i zmniejszanie stocku
            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);

                if ($item['quantity'] > $product->stock) {
                    DB::rollBack();
                    return redirect()->route('cart.index')
                        ->with('error', "Insufficient stock for product: {$product->title}. Only {$product->stock} left.");
                }

                // Zapisz pozycję zamówienia
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Zmniejsz stock
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            // Wyczyść koszyk
            session()->forget('cart');

            return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            logger('Error during checkout:', ['error' => $e->getMessage()]);
            return redirect()->route('cart.index')->with('error', 'An error occurred while placing the order.');
        }
    }
}
