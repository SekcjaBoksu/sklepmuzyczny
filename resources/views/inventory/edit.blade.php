@extends('layouts.app')

@section('title', 'Edytuj Produkt')

@section('content')
<div class="container">
    <h1>Edytuj Produkt</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('inventory.update', $product->id) }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="title" class="form-label">Tytuł Produktu</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $product->title }}" required aria-label="Edytuj tytuł produktu">
        </div>

        <div class="mb-3">
            <label for="artist" class="form-label">Artysta</label>
            <input type="text" name="artist" id="artist" class="form-control" value="{{ $product->artist }}" required aria-label="Edytuj nazwę artysty">
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Cena</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ $product->price }}" required aria-label="Edytuj cenę produktu">
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Stan Magazynowy</label>
            <input type="number" name="stock" id="stock" class="form-control" value="{{ $product->stock }}" required aria-label="Edytuj stan magazynowy produktu">
        </div>

        <div class="mb-3">
            <label for="format" class="form-label">Format</label>
            <select name="format" id="format" class="form-select" required aria-label="Wybierz format produktu">
                <option value="CD" {{ $product->format == 'CD' ? 'selected' : '' }}>CD</option>
                <option value="Vinyl" {{ $product->format == 'Vinyl' ? 'selected' : '' }}>Winyl</option>
                <option value="Special Edition" {{ $product->format == 'Special Edition' ? 'selected' : '' }}>Edycja Specjalna</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Kategoria</label>
            <select name="category_id" id="category_id" class="form-select" required aria-label="Wybierz kategorię produktu">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary" aria-label="Zaktualizuj produkt">Zaktualizuj Produkt</button>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary" aria-label="Anuluj edycję produktu">Anuluj</a>
    </form>
</div>
@endsection
