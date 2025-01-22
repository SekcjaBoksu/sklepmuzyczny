@extends('layouts.app')

@section('title', 'Twoje zamówienia')

@section('content')
<div class="container">
    <h1>Twoje zamówienia</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        // Sortowanie zamówień: W trakcie realizacji najpierw, potem według daty
        $sortedOrders = $orders->sortByDesc(function ($order) {
            return $order->status === 'pending' ? 1 : 0;
        })->sortByDesc('created_at');
    @endphp

    @if($sortedOrders->isEmpty())
        <p>Nie masz jeszcze żadnych zamówień.</p>
    @else
        <div class="accordion" id="ordersAccordion">
            @foreach($sortedOrders as $order)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-{{ $order->id }}">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $order->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-{{ $order->id }}" 
                                style="background-color: 
                                    {{ $order->is_paid && $order->shipment_status === 'wysłana' ? '#d4edda' : ($order->status === 'pending' ? '#fff3cd' : '') }};">
                            Zamówienie #{{ $order->id }} - 
                            @if($order->is_paid && $order->shipment_status === 'wysłana')
                                <span class="text-success">Zakończone</span>
                            @else
                                {{ ucfirst($order->status) }}
                            @endif
                            ({{ $order->created_at->format('Y-m-d H:i') }})
                        </button>
                    </h2>
                    <div id="collapse-{{ $order->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading-{{ $order->id }}" data-bs-parent="#ordersAccordion">
                        <div class="accordion-body">
                            <p><strong>Łączna cena:</strong> {{ number_format($order->total_price, 2) }} zł</p>
                            <p><strong>Status płatności:</strong> 
                                <span class="{{ $order->is_paid ? 'text-success' : 'text-danger' }}">
                                    {{ $order->is_paid ? 'Opłacone' : 'Nieopłacone' }}
                                </span>
                                @if(!$order->is_paid)
                                    <a href="{{ route('orders.pay', $order->id) }}" class="btn btn-sm btn-primary ms-3" aria-label="Opłać zamówienie #{{ $order->id }}">Opłać teraz</a>
                                @endif
                            </p>
                            <p><strong>Status wysyłki:</strong> {{ ucfirst($order->shipment_status) }}</p>
                            <p><strong>Adres:</strong> {{ $order->address->address_line1 }}, {{ $order->address->city }}, {{ $order->address->postal_code }}</p>
                            
                            <h5>Produkty:</h5>
                            <ul>
                                @foreach($order->items as $item)
                                    <li>{{ $item->product->title }} - {{ number_format($item->price, 2) }} zł x {{ $item->quantity }}</li>
                                @endforeach
                            </ul>

                            @if(!$order->is_paid && $order->shipment_status === 'przyjęta')
                                <form method="POST" action="{{ route('orders.cancel', $order->id) }}" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" aria-label="Anuluj zamówienie #{{ $order->id }}">Anuluj zamówienie</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
