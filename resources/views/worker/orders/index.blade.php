@extends('layouts.app')

@section('title', 'Zarządzanie zamówieniami')

@section('content')
<div class="container">
    <h1>Zarządzanie zamówieniami</h1>

    <!-- Sekcja filtrowania -->
    <form method="GET" action="{{ route('worker.orders.index') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="order_id" class="form-label">ID zamówienia</label>
                <input type="text" name="order_id" id="order_id" class="form-control" aria-label="Filtruj według ID zamówienia" value="{{ request('order_id') }}">
            </div>
            <div class="col-md-3">
                <label for="user_name" class="form-label">Nazwa użytkownika</label>
                <input type="text" name="user_name" id="user_name" class="form-control" aria-label="Filtruj według nazwy użytkownika" value="{{ request('user_name') }}">
            </div>
            <div class="col-md-3">
                <label for="date" class="form-label">Data</label>
                <input type="date" name="date" id="date" class="form-control" aria-label="Filtruj według daty" value="{{ request('date') }}">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary w-100" aria-label="Zastosuj filtry">Filtruj</button>
            </div>
        </div>
    </form>

    <!-- Tabela zamówień -->
    <table class="table table-bordered" aria-label="Tabela zamówień">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Użytkownik</th>
                <th>Całkowita cena</th>
                <th>Opłacone</th>
                <th>Status wysyłki</th>
                <th>Data zamówienia</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>{{ number_format($order->total_price, 2) }} zł</td>
                    <td>
                        <span class="badge {{ $order->is_paid ? 'bg-success' : 'bg-danger' }}" aria-label="{{ $order->is_paid ? 'Zamówienie opłacone' : 'Zamówienie nieopłacone' }}">
                            {{ $order->is_paid ? 'Tak' : 'Nie' }}
                        </span>
                    </td>
                    <td>
                        <!-- Aktualizacja statusu wysyłki -->
                        <form method="POST" action="{{ route('worker.orders.updateShipmentStatus', $order) }}" class="d-inline update-shipment-status-form">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="shipment_status" value="przyjęta">
                            <input type="checkbox" name="shipment_status" class="form-check-input shipment-status-checkbox"
                                value="wysłana" {{ $order->shipment_status === 'wysłana' ? 'checked' : '' }} aria-label="Zmień status wysyłki">
                        </form>
                    </td>
                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <!-- Podgląd/Edycja zamówienia -->
                        <a href="{{ route('worker.orders.edit', $order) }}" class="btn btn-warning btn-sm" aria-label="Edytuj zamówienie {{ $order->id }}">Edytuj</a>

                        <!-- Usuń zamówienie -->
                        <form method="POST" action="{{ route('worker.orders.destroy', $order) }}" class="d-inline delete-order-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm delete-order-btn" aria-label="Usuń zamówienie {{ $order->id }}">
                                Usuń
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Nie znaleziono zamówień.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginacja -->
    <div class="d-flex justify-content-center">
        {{ $orders->appends(request()->query())->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Potwierdzenie usunięcia zamówienia
        document.querySelectorAll('.delete-order-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                if (confirm('Czy na pewno chcesz usunąć to zamówienie? Tej akcji nie można cofnąć.')) {
                    this.closest('.delete-order-form').submit();
                }
            });
        });

        // Automatyczne przesyłanie statusu wysyłki po zmianie checkboxa
        document.querySelectorAll('.shipment-status-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                this.closest('.update-shipment-status-form').submit();
            });
        });
    });
</script>
@endsection
