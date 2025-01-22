@extends('layouts.app')

@section('title', 'Twój Koszyk')

@section('content')
<div class="container">
    <h1>Twój Koszyk</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(!empty($cart) && count($cart) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Cena</th>
                    <th>Ilość</th>
                    <th>Dostępny Stan</th>
                    <th>Razem</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $id => $item)
                    @php $product = \App\Models\Product::find($id); @endphp
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ number_format($item['price'], 2) }} zł</td>
                        <td>
                            <form method="POST" action="{{ route('cart.update', $id) }}" class="d-inline">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" 
                                    min="1" max="{{ $product->stock }}" 
                                    class="form-control d-inline w-50" style="width: 70px;">
                                <button type="submit" class="btn btn-primary btn-sm">Aktualizuj</button>
                            </form>
                        </td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ number_format($item['price'] * $item['quantity'], 2) }} zł</td>
                        <td>
                            <form method="POST" action="{{ route('cart.remove', $id) }}">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Usuń</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Łączna Kwota: {{ number_format($total, 2) }} zł</h3>

        <!-- Warunkowe wyświetlenie przycisku "Przejdź do zamówienia" -->
        @auth
            <a href="{{ route('checkout.create') }}" class="btn btn-success">Przejdź do zamówienia</a>
        @else
            <button class="btn btn-success" disabled>Przejdź do zamówienia (wymagane logowanie)</button>
            <p class="mt-2 text-danger">Zaloguj się, aby kontynuować: <a href="{{ route('login') }}">zaloguj się</a>.</p>
        @endauth
    @else
        <div class="alert alert-info mt-4" role="alert">
            <strong>Twój koszyk jest pusty!</strong> Przejrzyj produkty i dodaj przedmioty do koszyka.
            <a href="{{ route('products.index') }}" class="btn btn-link">Przejdź do produktów</a>
        </div>
    @endif
</div>
@endsection
