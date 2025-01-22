@extends('layouts.app')

@section('title', $product->title)

@section('content')
<div class="container">
    <!-- Informacje o produkcie -->
    <h1 class="mb-3">
        {{ $product->title }}
        @auth
            @if(Auth::user()->favorites->contains('product_id', $product->id))
                <span class="text-danger" aria-label="Produkt dodany do ulubionych">❤︎</span>
            @endif
        @endauth
    </h1>
    <p><strong>Artysta:</strong> {{ $product->artist }}</p>
    <p><strong>Cena:</strong> {{ number_format($product->price, 2) }} zł</p>
    <p><strong>Format:</strong> {{ $product->format }}</p>
    <p><strong>Kategoria:</strong> {{ $product->category->name ?? 'Brak kategorii' }}</p>
    <p>
        <strong>Dostępność:</strong>
        @if ($product->stock > 20)
            <span class="text-success" aria-label="Produkt dostępny ({{ $product->stock }} w magazynie)">Dostępny ({{ $product->stock }})</span>
        @elseif ($product->stock > 0)
            <span class="text-warning" aria-label="Ograniczona dostępność ({{ $product->stock }} w magazynie)">Ograniczona dostępność ({{ $product->stock }})</span>
        @else
            <span class="text-danger" aria-label="Brak w magazynie">Brak w magazynie</span>
        @endif
    </p>

    <p>
        <strong>Średnia ocena:</strong>
        @php
            $averageRating = $product->reviews->avg('rating') ?? 0;
            $filledStars = str_repeat('★', floor($averageRating));
            $emptyStars = str_repeat('☆', 5 - floor($averageRating));
        @endphp
        <span aria-label="Ocena produktu: {{ number_format($averageRating, 1) }}/5">{{ $filledStars }}{{ $emptyStars }} ({{ number_format($averageRating, 1) }}/5)</span>
    </p>

    <!-- Dodaj do koszyka i ulubionych -->
    <div class="d-flex gap-3 align-items-center">
        @if ($product->stock > 0)
            <form method="POST" action="{{ route('cart.add', $product->id) }}">
                @csrf
                <button type="submit" class="btn btn-primary" aria-label="Dodaj produkt do koszyka">Dodaj do koszyka</button>
            </form>
        @else
            <p class="text-danger mb-0">Ten produkt jest obecnie niedostępny.</p>
        @endif

        @auth
            @if(Auth::user()->favorites->contains('product_id', $product->id))
                <form method="POST" action="{{ route('favorites.destroy', $product->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" aria-label="Usuń produkt z ulubionych">
                        <i class="fas fa-heart-broken"></i> Usuń z ulubionych
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('favorites.store', $product->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" aria-label="Dodaj produkt do ulubionych">
                        <i class="fas fa-heart"></i> Dodaj do ulubionych
                    </button>
                </form>
            @endif
        @endauth
    </div>

    <!-- Sekcja recenzji -->
    <h3 class="mt-5">Recenzje</h3>
    @if($product->reviews->isEmpty())
        <p class="text-muted">Brak recenzji. Bądź pierwszy, który doda recenzję!</p>
    @else
        <ul class="list-group mb-4">
            @foreach($product->reviews as $review)
                <li class="list-group-item">
                    <strong>{{ $review->user->name ?? 'Nieznany użytkownik' }}</strong>
                    <span class="text-muted" aria-label="Ocena użytkownika">
                        ({{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }})
                    </span>
                    <span class="text-muted d-block">Dodano: {{ $review->created_at->format('d M Y, H:i') }}</span>
                    <p>{{ $review->review }}</p>
                    @auth
                        @if(auth()->id() === $review->user_id || (auth()->user() && auth()->user()->role === 'admin'))
                            <form method="POST" action="{{ route('reviews.destroy', $review) }}" onsubmit="return confirm('Czy na pewno chcesz usunąć tę recenzję?');" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" aria-label="Usuń recenzję użytkownika {{ $review->user->name ?? 'Nieznany użytkownik' }}">Usuń</button>
                            </form>
                        @endif
                    @endauth
                </li>
            @endforeach
        </ul>
    @endif

    <!-- Dodawanie recenzji -->
    @auth
        <h4>Dodaj recenzję</h4>
        <form method="POST" action="{{ route('reviews.store', $product->id) }}">
            @csrf
            <div class="mb-3">
                <label for="rating" class="form-label">Ocena</label>
                <select name="rating" id="rating" class="form-select" required aria-label="Wybierz ocenę produktu">
                    <option value="" disabled selected>Wybierz ocenę</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="mb-3">
                <label for="review" class="form-label">Recenzja</label>
                <textarea name="review" id="review" class="form-control" rows="3" required aria-label="Dodaj treść recenzji"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" aria-label="Prześlij recenzję">Dodaj recenzję</button>
        </form>
    @else
        <p><a href="{{ route('login') }}" aria-label="Zaloguj się, aby dodać recenzję">Zaloguj się</a>, aby dodać recenzję.</p>
    @endauth

    <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3" aria-label="Powrót do listy produktów">Powrót do produktów</a>
</div>
@endsection
