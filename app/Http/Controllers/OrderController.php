<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.product', 'address'])
            ->where('user_id', auth()->id())
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Koszyk jest pusty.');
        }

        DB::beginTransaction();

        try {
            // Sprawdź dostępność produktów
            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);

                if ($item['quantity'] > $product->stock) {
                    return redirect()->route('cart.index')
                        ->with('error', "Insufficient stock for product: {$product->title}. Only {$product->stock} left.");
                }
            }

            // Tworzenie zamówienia
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => array_reduce($cart, function ($carry, $item) {
                    return $carry + ($item['price'] * $item['quantity']);
                }, 0),
                'status' => 'pending',
            ]);

            // Przetwarzanie pozycji zamówienia
            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);

                if ($item['quantity'] > $product->stock) {
                    DB::rollBack();
                    return redirect()->route('cart.index')
                        ->with('error', "Insufficient stock for product: {$product->title}. Please adjust your cart.");
                }

                // Zapisz element zamówienia
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

            return redirect()->route('orders.index')->with('success', 'Zamówienie przyjęte!');
        } catch (\Exception $e) {
            DB::rollBack();
            logger('Order creation failed:', ['error' => $e->getMessage()]);
            return redirect()->route('cart.index')->with('error', 'An error occurred while placing the order.');
        }
    }

    public function cancel(Order $order)
    {
        if ($order->is_paid || $order->shipment_status !== 'przyjęta') {
            return redirect()->back()->with('error', 'Zamówienie nie może zostać anulowane.');
        }

        DB::beginTransaction();

        try {
            foreach ($order->items as $item) {
                $product = Product::findOrFail($item->product_id);
                $product->increment('stock', $item->quantity);
            }

            $order->items()->delete();
            $order->address()->delete();
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Zamówienie.');
        } catch (\Exception $e) {
            DB::rollBack();
            logger('Order cancellation failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Wystąpił błąd w trakcie składania zamówienia.');
        }
    }


    //przetwarzanie płatnośći
    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->is_paid = $request->has('is_paid') ? $request->is_paid : false;
        $order->save();

        return back()->with('success', 'Status płatności zaktualizowany.');
    }

    public function updateShipmentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $allowedStatuses = ['przyjęta', 'wysłana', 'odebrana'];
        $newStatus = $request->input('shipment_status');

        if (!in_array($newStatus, $allowedStatuses)) {
            return back()->with('error', 'Nieprawidłowy status wysyłki.');
        }

        $order->shipment_status = $newStatus;
        $order->save();

        return back()->with('success', 'Status wysyłki zaktualizowany.');
    }

    public function showPayment($id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        return view('orders.payment', compact('order'));
    }

    public function processPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Symulacja płatności (tutaj możesz dodać zewnętrzne API)
        $order->is_paid = true;
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Płatnośc powiodła się!');
    }

}
