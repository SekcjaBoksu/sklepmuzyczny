@extends('layouts.app')

@section('title', 'Płatność za zamówienie #' . $order->id)

@section('content')
<div class="container">
    <h1>Płatność za zamówienie #{{ $order->id }}</h1>

    <p><strong>Łączna cena:</strong> {{ number_format($order->total_price, 2) }} zł</p>
    <p><strong>Status płatności:</strong> 
        <span class="{{ $order->is_paid ? 'text-success' : 'text-danger' }}">
            {{ $order->is_paid ? 'Opłacone' : 'Nieopłacone' }}
        </span>
    </p>

    @if(!$order->is_paid)
        <form method="POST" action="{{ route('orders.processPayment', $order->id) }}">
            @csrf
            <button type="submit" class="btn btn-primary" aria-label="Opłać zamówienie">Opłać teraz</button>
        </form>
    @else
        <p class="text-success">To zamówienie jest już opłacone.</p>
    @endif

    <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3" aria-label="Powrót do listy zamówień">Powrót do zamówień</a>
</div>
@endsection
