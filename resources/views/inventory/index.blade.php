@extends('layouts.app')

@section('title', 'Zarządzanie Magazynem')

@section('content')
<div class="container">
    <h1>Zarządzanie Magazynem</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Dodawanie nowego produktu (tylko admin) -->
    @if(auth()->user()->role === 'admin')
        <div class="mb-4">
            <h3>Dodaj Nowy Produkt</h3>
            <form method="POST" action="{{ route('inventory.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="title" class="form-label">Tytuł</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="artist" class="form-label">Artysta</label>
                        <input type="text" name="artist" id="artist" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="price" class="form-label">Cena</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="stock" class="form-label">Stan Magazynowy</label>
                        <input type="number" name="stock" id="stock" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label for="format" class="form-label">Format</label>
                        <select name="format" id="format" class="form-select" required>
                            <option value="CD">CD</option>
                            <option value="Vinyl">Winyl</option>
                            <option value="Special Edition">Edycja Specjalna</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="category_id" class="form-label">Kategoria</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success mt-3">Dodaj Produkt</button>
            </form>
        </div>
    @endif

<!-- Filtry i sortowanie -->
<div class="mb-4">
    <form method="GET" action="{{ route('inventory.index') }}" class="row row-cols-lg-auto g-3 align-items-center">
        <div class="col">
            <label for="product_id" class="visually-hidden">ID Produktu</label>
            <input type="text" id="product_id" name="product_id" class="form-control" placeholder="ID Produktu"
                   value="{{ request('product_id') }}">
        </div>
        <div class="col">
            <label for="search" class="visually-hidden">Szukaj</label>
            <input type="text" id="search" name="search" class="form-control" placeholder="Tytuł lub Artysta"
                   value="{{ request('search') }}">
        </div>
        <div class="col">
            <label for="category" class="visually-hidden">Gatunek</label>
            <select name="category" class="form-select" id="category">
                <option value="">Wszystkie Gatunki</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label for="format" class="visually-hidden">Format</label>
            <select name="format" id="format" class="form-select">
                <option value="">Wszystkie Format</option>
                <option value="CD" {{ request('format') == 'CD' ? 'selected' : '' }}>CD</option>
                <option value="Vinyl" {{ request('format') == 'Vinyl' ? 'selected' : '' }}>Winyl</option>
                <option value="Special Edition" {{ request('format') == 'Special Edition' ? 'selected' : '' }}>Edycja Specjalna</option>
            </select>
        </div>
        <div class="col">
            <label for="sort" class="visually-hidden">Sortuj wg</label>
            <select name="sort" id="sort" class="form-select">
                <option value="">Domyślne</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Cena: od najniższej</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Cena: od najwyższej</option>
            </select>
        </div>
        <div class="col">
            <button type="submit" class="btn btn-primary">Filtruj</button>
        </div>
    </form>
</div>

    <!-- Lista produktów -->
    <h3>Lista Produktów</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tytuł</th>
                <th>Artysta</th>
                <th>Cena</th>
                <th>Stan Magazynowy</th>
                <th>Format</th>
                <th>Kategoria</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->title }}</td>
                    <td>{{ $product->artist }}</td>
                    <td>{{ number_format($product->price, 2) }} zł</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->format }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>
                        <a href="{{ route('inventory.edit', $product) }}" class="btn btn-warning btn-sm">Edytuj</a>
                        @if(auth()->user()->role === 'admin')
                            <form method="POST" action="{{ route('inventory.destroy', $product) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm delete-product-btn" aria-label="Usuń produkt {{ $product->title }}">Usuń</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Nie znaleziono produktów.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginacja -->
    <div class="d-flex justify-content-center">
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-product-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                if (confirm('Czy na pewno chcesz usunąć ten produkt?')) {
                    this.closest('form').submit();
                }
            });
        });
    });
</script>
@endsection
