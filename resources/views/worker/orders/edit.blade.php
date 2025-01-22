@extends('layouts.app')

@section('title', 'Edytuj zamówienie')

@section('content')
<div class="container">
    <h1>Edytuj zamówienie #{{ $order->id }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Edycja adresu -->
    <form id="local-edit-form">
        <h4>Adres</h4>
        <div class="mb-3">
            <label for="address_line1" class="form-label">Adres - linia 1</label>
            <input type="text" name="address_line1" id="address_line1" class="form-control" aria-label="Edytuj adres - linia 1" value="{{ $order->address->address_line1 }}">
        </div>
        <div class="mb-3">
            <label for="city" class="form-label">Miasto</label>
            <input type="text" name="city" id="city" class="form-control" aria-label="Edytuj miasto" value="{{ $order->address->city }}">
        </div>
        <div class="mb-3">
            <label for="postal_code" class="form-label">Kod pocztowy</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control" aria-label="Edytuj kod pocztowy" value="{{ $order->address->postal_code }}">
        </div>

        <h4>Produkty</h4>
        <table class="table" aria-label="Tabela produktów">
            <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Cena</th>
                    <th>Ilość</th>
                    <th>Łącznie</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody id="products-table">
                @foreach($order->items as $item)
                    <tr data-item-id="{{ $item->id }}">
                        <td>{{ $item->product->title }}</td>
                        <td>{{ number_format($item->price, 2) }} zł</td>
                        <td>
                            <input type="number" name="quantities[{{ $item->id }}]" value="{{ $item->quantity }}" min="1" class="form-control w-50" aria-label="Zmień ilość produktu {{ $item->product->title }}">
                        </td>
                        <td class="total-price">{{ number_format($item->price * $item->quantity, 2) }} zł</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item-btn" data-item-id="{{ $item->id }}" aria-label="Usuń produkt {{ $item->product->title }}">Usuń</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" id="save-changes-btn" class="btn btn-secondary" aria-label="Zapisz zmiany lokalne">Zapisz zmiany</button>
        <button type="submit" class="btn btn-primary" form="confirm-edit-form" aria-label="Zatwierdź zmiany">Zatwierdź</button>
        <a href="{{ route('worker.orders.index') }}" class="btn btn-secondary" aria-label="Powrót do listy zamówień">Powrót</a>
    </form>

    <!-- Formularz zatwierdzający -->
    <form method="POST" action="{{ route('worker.orders.update', $order) }}" id="confirm-edit-form" style="display:none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="address_line1" id="confirm-address-line1">
        <input type="hidden" name="city" id="confirm-city">
        <input type="hidden" name="postal_code" id="confirm-postal-code">
        <input type="hidden" name="removed_items" id="confirm-removed-items">
        @foreach($order->items as $item)
            <input type="hidden" name="quantities[{{ $item->id }}]" id="confirm-quantity-{{ $item->id }}">
        @endforeach
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const saveChangesBtn = document.getElementById('save-changes-btn');
        const confirmEditForm = document.getElementById('confirm-edit-form');
        let removedItems = [];

        saveChangesBtn.addEventListener('click', () => {
            // Aktualizuj lokalne wartości
            document.querySelectorAll('.total-price').forEach((cell) => {
                const row = cell.closest('tr');
                const price = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace(' zł', '').replace(',', '.'));
                const quantity = parseInt(row.querySelector('input[type="number"]').value);
                cell.textContent = `${(price * quantity).toFixed(2)} zł`;
            });
        });

        // Przygotuj dane do zatwierdzenia
        confirmEditForm.addEventListener('submit', () => {
            document.getElementById('confirm-address-line1').value = document.getElementById('address_line1').value;
            document.getElementById('confirm-city').value = document.getElementById('city').value;
            document.getElementById('confirm-postal-code').value = document.getElementById('postal_code').value;

            document.querySelectorAll('input[name^="quantities"]').forEach((input) => {
                const itemId = input.name.match(/\d+/)[0];
                document.getElementById(`confirm-quantity-${itemId}`).value = input.value;
            });

            document.getElementById('confirm-removed-items').value = JSON.stringify(removedItems);
        });

        // Usuwanie produktu
        document.querySelectorAll('.remove-item-btn').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                const itemId = e.target.getAttribute('data-item-id');
                const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
                const rowCount = document.querySelectorAll('#products-table tr').length;

                if (rowCount > 1) {
                    removedItems.push(itemId);
                    row.style.display = 'none';
                } else {
                    alert('Zamówienie musi zawierać co najmniej jeden produkt.');
                }
            });
        });
    });
</script>
@endsection
