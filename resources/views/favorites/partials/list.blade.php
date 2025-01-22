<div class="row">
    @foreach($favorites as $favorite)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $favorite->product->title }}</h5>
                    <p class="card-text flex-grow-1">
                        <strong>Artysta:</strong> {{ $favorite->product->artist }}<br>
                        <strong>Cena:</strong> {{ number_format($favorite->product->price, 2) }} zł<br>
                        <strong>Format:</strong> {{ $favorite->product->format }}<br>
                        <strong>Kategoria:</strong> {{ $favorite->product->category->name ?? 'Brak' }}<br>
                    </p>
                    <form method="POST" action="{{ route('favorites.destroy', $favorite->product) }}" class="mt-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-heart"></i> Usuń z ulubionych
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($favorites->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $favorites->links() }}
    </div>
@endif
