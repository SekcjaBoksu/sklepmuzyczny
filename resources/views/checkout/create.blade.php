@extends('layouts.app')

@section('title', 'Zamówienie')

@section('content')
<div class="container">
    <h1>Zamówienie</h1>

    <h3>Twój Koszyk</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Cena</th>
                <th>Ilość</th>
                <th>Razem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart as $id => $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ number_format($item['price'], 2) }} zł</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['price'] * $item['quantity'], 2) }} zł</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h3>Łączna kwota: {{ number_format($total, 2) }} zł</h3>

    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf

        <h3>Adres Dostawy</h3>
        <div class="mb-3">
            <label for="address_line1" class="form-label">Adres</label>
            <input type="text" class="form-control" name="address_line1" id="address_line1" required>
        </div>

        <div class="mb-3">
            <label for="city" class="form-label">Miasto</label>
            <input type="text" class="form-control" name="city" id="city" required>
        </div>

        <div class="mb-3">
            <label for="postal_code" class="form-label">Kod Pocztowy</label>
            <input type="text" class="form-control" name="postal_code" id="postal_code" required>
        </div>

        <div class="mb-3">
            <label for="country" class="form-label">Kraj</label>
            <input type="text" class="form-control" name="country" id="country" required>
        </div>

        <button type="submit" class="btn btn-success">Złóż zamówienie</button>
    </form>
</div>
@endsection
