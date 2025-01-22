<div class="row">
    @forelse ($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <!-- Link na całą kartę -->
                <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark d-block h-100" aria-label="Zobacz szczegóły produktu {{ $product->title }}">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->title }}</h5>
                        <p class="card-text flex-grow-1">
                            <strong>Artysta:</strong> {{ $product->artist }}<br>
                            <strong>Cena:</strong> {{ number_format($product->price, 2) }} zł<br>
                            <strong>Format:</strong> {{ $product->format }}<br>
                            <strong>Kategoria:</strong> {{ $product->category->name ?? 'Brak kategorii' }}<br>
                            <strong>Dostępność:</strong> 
                            @if ($product->stock > 20)
                                <span class="text-success" aria-label="Produkt dostępny">Dostępny</span>
                            @elseif ($product->stock > 0)
                                <span class="text-warning" aria-label="Produkt dostępny w ograniczonej ilości">Ograniczona dostępność</span>
                            @else
                                <span class="text-danger" aria-label="Produkt niedostępny">Brak w magazynie</span>
                            @endif
                        </p>
                    </div>
                </a>
                <!-- Przyciski akcji -->
                <div class="card-footer bg-white border-top-0">
                <form method="POST" action="{{ route('cart.add', $product->id) }}?{{ request()->getQueryString() }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100" {{ $product->stock === 0 ? 'disabled' : '' }} aria-label="{{ $product->stock === 0 ? 'Produkt niedostępny, nie można dodać do koszyka' : 'Dodaj produkt do koszyka' }}">
                        <i class="fas fa-cart-plus"></i> Dodaj do koszyka
                    </button>
                </form>

                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p class="text-center text-muted fs-5">Nie znaleziono produktów. Spróbuj zmienić filtry.</p>
        </div>
    @endforelse
</div>

@if ($products->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
@endif
