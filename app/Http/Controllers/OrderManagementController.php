<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OrderManagementController extends Controller
{
    /**
     * Wyświetla listę zamówień z możliwością filtrowania.
     */
    public function index(Request $request)
    {
        // Sprawdzanie roli użytkownika
        if (!in_array(auth()->user()->role, ['employee', 'admin'])) {
            abort(403, 'Access denied');
        }

        // Pobieranie zamówień z relacjami
        $query = Order::with('user', 'items.product');

        // Filtracja po ID zamówienia
        if ($request->filled('order_id')) {
            $query->where('id', $request->order_id);
        }

        // Filtracja po nazwie użytkownika
        if ($request->filled('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_name . '%');
            });
        }

        // Filtracja po dacie
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Paginacja zamówień
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('worker.orders.index', compact('orders'));
    }

    public function edit(Order $order)
    {
        if (!in_array(auth()->user()->role, ['employee', 'admin'])) {
            abort(403, 'Access denied');
        }

        $order->load('items.product', 'address'); // Ładowanie powiązanych produktów i adresu
        return view('worker.orders.edit', compact('order'));
    }

    public function updateItem(Request $request, Order $order)
    {
        if (!in_array(auth()->user()->role, ['employee', 'admin'])) {
            abort(403, 'Access denied');
        }

        // Walidacja danych
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'item_id' => 'required|exists:order_items,id',
        ]);

        // Znajdź element zamówienia i zaktualizuj ilość
        $item = $order->items()->findOrFail($request->item_id);
        $item->update(['quantity' => $request->quantity]);

        // Przelicz całkowitą wartość zamówienia
        $totalPrice = $order->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Zaktualizuj w zamówieniu
        $order->update(['total_price' => $totalPrice]);

        return redirect()->back()->with('success', 'Produkt z zamówienia zaktualizowany!');
    }

    public function destroy(Order $order)
    {
        if (!in_array(auth()->user()->role, ['employee', 'admin'])) {
            abort(403, 'Access denied');
        }
    
        // Rozpoczynamy transakcję
        DB::beginTransaction();
    
        try {
            // Przywrócenie stanu magazynowego produktów
            foreach ($order->items as $item) {
                $product = Product::findOrFail($item->product_id);
                $product->increment('stock', $item->quantity); // Zwiększamy stock
            }
    
            // Usuwamy pozycje zamówienia, adres i samo zamówienie
            $order->items()->delete();
            $order->address()->delete();
            $order->delete();
    
            // Zatwierdzamy transakcję
            DB::commit();
    
            return redirect()->route('worker.orders.index')->with('success', 'Zamówienie usunięte.');
        } catch (\Exception $e) {
            // Jeśli wystąpi błąd, anulujemy transakcję
            DB::rollBack();
            logger('Order deletion failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while deleting the order.');
        }
    }
    



    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'address_line1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'quantities' => 'sometimes|array',
            'quantities.*' => 'integer|min:1',
            'removed_items' => 'sometimes|json',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Aktualizuj adres
            $order->address->update([
                'address_line1' => $validated['address_line1'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
            ]);
    
            // Usuń produkty
            if (!empty($validated['removed_items'])) {
                $removedItems = json_decode($validated['removed_items'], true);
                foreach ($removedItems as $itemId) {
                    $orderItem = $order->items()->find($itemId);
                    if ($orderItem) {
                        // Zwiększ stock o usuniętą ilość
                        $product = $orderItem->product;
                        $product->increment('stock', $orderItem->quantity);
                        $orderItem->delete();
                    }
                }
            }
    
            // Aktualizuj ilości produktów
            if (!empty($validated['quantities'])) {
                foreach ($validated['quantities'] as $itemId => $newQuantity) {
                    $orderItem = $order->items()->find($itemId);
                    if ($orderItem) {
                        $product = $orderItem->product;
                        $originalQuantity = $orderItem->quantity;
    
                        if ($newQuantity > $originalQuantity) {
                            // Zmniejsz stock
                            $difference = $newQuantity - $originalQuantity;
                            if ($difference > $product->stock) {
                                throw new \Exception("Nie wystarczająca ilość: {$product->title}. Pozostały jedynie {$product->stock} sztuki.");
                            }
                            $product->decrement('stock', $difference);
                        } elseif ($newQuantity < $originalQuantity) {
                            // Zwiększ stock
                            $difference = $originalQuantity - $newQuantity;
                            $product->increment('stock', $difference);
                        }
    
                        // Zaktualizuj ilość w zamówieniu
                        $orderItem->update(['quantity' => $newQuantity]);
                    }
                }
            }
    
            // Oblicz nową sumę
            $totalPrice = $order->items->reduce(function ($carry, $item) {
                return $carry + ($item->price * $item->quantity);
            }, 0);
    
            $order->update(['total_price' => $totalPrice]);
    
            DB::commit();
    
            return redirect()->route('worker.orders.edit', $order)
                ->with('success', 'Zamówienie zaktualizowane pomyślnie.');
        } catch (\Exception $e) {
            DB::rollBack();
            logger('Order update failed:', ['error' => $e->getMessage()]);
            return redirect()->route('worker.orders.edit', $order)
                ->with('error', 'Wystąpił błąd przy aktualizowaniu zamówienia.');
        }
    }
    
    
    
    /**
     * Aktualizuje status płatności zamówienia.
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        // Sprawdzanie roli użytkownika
        if (!in_array(auth()->user()->role, ['employee', 'admin'])) {
            abort(403, 'Access denied');
        }

        // Walidacja danych
        $request->validate([
            'is_paid' => 'required|boolean',
        ]);

        $order->update(['is_paid' => $request->is_paid]);

        return redirect()->back()->with('success', 'Status płatności zaktualizowany pomyślnie.');
    }

    /**
     * Aktualizuje status przesyłki zamówienia.
     */
    public function updateShipmentStatus(Request $request, Order $order)
    {
        if (!in_array(auth()->user()->role, ['employee', 'admin'])) {
            abort(403, 'Access denied');
        }
    
        $request->validate([
            'shipment_status' => 'required|in:przyjęta,wysłana',
        ]);
    
        $order->shipment_status = $request->shipment_status;
        $order->save();
    
        return redirect()->route('worker.orders.index')->with('success', 'Status przesyłki zaktualizowany pomyślnie.');
    }
    
    

}
