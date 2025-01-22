<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moje Ulubione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/accessibility.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Nawigacja -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">♫ Sklep Muzyczny</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Rozwiń nawigację">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('products') ? 'active' : '' }}" href="{{ route('products.index') }}">Strona Główna</a>
                    </li>
                    @if(Auth::check() && (Auth::user()->role === 'employee' || Auth::user()->role === 'admin'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('worker/orders') ? 'active' : '' }}" href="{{ route('worker.orders.index') }}">Panel Pracownika</a>
                        </li>
                    @endif
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Panel Administratora</a>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart"></i> Koszyk 🛒
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                                {{ session('cart') ? count(session('cart')) : 0 }}
                            </span>
                            ({{ number_format(session('cart') ? array_reduce(session('cart'), function ($carry, $item) {
                                return $carry + ($item['price'] * $item['quantity']);
                            }, 0) : 0, 2) }} zł)
                        </a>
                    </li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('orders.index') }}">Moje Zamówienia</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('favorites.index') }}">Ulubione ❤︎</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Wyloguj się</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Zaloguj się</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Zarejestruj się</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Przyciski dostępności -->
    <div class="container mt-2">
        <div class="d-flex gap-2 justify-content-end">
            <button id="toggle-contrast" class="btn btn-outline-secondary btn-sm">Tryb wysoki kontrast</button>
            <button id="increase-font" class="btn btn-outline-secondary btn-sm">Powiększ czcionkę</button>
            <button id="decrease-font" class="btn btn-outline-secondary btn-sm">Pomniejsz czcionkę</button>
        </div>
    </div>

    <!-- Główna Treść -->
    <div class="container mt-5">
        <h1 class="text-center mb-4">Moje Ulubione</h1>

        <!-- Filtry i Sortowanie -->
        <form id="filter-form" class="row row-cols-lg-auto g-3 align-items-center justify-content-center mb-4">
            <div class="col">
                <label for="search" class="visually-hidden">Szukaj</label>
                <input type="text" id="search" name="search" class="form-control form-control-sm" 
                    placeholder="Szukaj" value="{{ request('search', '') }}">
            </div>
            <div class="col">
                <label for="category" class="visually-hidden">Kategoria</label>
                <select name="category" class="form-select form-select-sm" id="category">
                    <option value="">Wszystkie Kategorie</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" 
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label for="format" class="visually-hidden">Format</label>
                <select name="format" class="form-select form-select-sm" id="format">
                    <option value="">Wszystkie Format</option>
                    <option value="CD" {{ request('format') == 'CD' ? 'selected' : '' }}>CD</option>
                    <option value="Vinyl" {{ request('format') == 'Vinyl' ? 'selected' : '' }}>Winyl</option>
                    <option value="Special Edition" {{ request('format') == 'Special Edition' ? 'selected' : '' }}>Edycja Specjalna</option>
                </select>
            </div>
            <div class="col">
                <label for="sort" class="visually-hidden">Sortuj wg</label>
                <select name="sort" class="form-select form-select-sm" id="sort">
                    <option value="">Domyślne</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Cena: od najniższej</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Cena: od najwyższej</option>
                </select>
            </div>
            <div class="col">
                <button type="button" id="reset-filters" class="btn btn-outline-secondary btn-sm">↺</button>
            </div>
        </form>

        <!-- Lista Ulubionych -->
        <div id="favorite-list" class="row">
            @if($favorites->isEmpty())
                @for($i = 0; $i < 12; $i++) {{-- Placeholdery --}} 
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 placeholder-card">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Brak Produktu</h5>
                                <p class="card-text flex-grow-1">
                                    <strong>Artysta:</strong> Brak<br>
                                    <strong>Cena:</strong> 0.00 zł<br>
                                    <strong>Format:</strong> Brak<br>
                                    <strong>Kategoria:</strong> Brak<br>
                                </p>
                                <button class="btn btn-outline-secondary w-100 disabled">Brak Akcji</button>
                            </div>
                        </div>
                    </div>
                @endfor
            @else
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
                                        <i class="fas fa-heart"></i> Usuń z Ulubionych
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if($favorites->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $favorites->links() }}
            </div>
        @endif
    </div>

    <!-- Skrypty -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleContrastBtn = document.getElementById('toggle-contrast');
        const increaseFontBtn = document.getElementById('increase-font');
        const decreaseFontBtn = document.getElementById('decrease-font');
        const body = document.body;

        // Tryb wysokiego kontrastu
        if (localStorage.getItem('highContrast') === 'enabled') {
            body.classList.add('high-contrast');
        }

        if (toggleContrastBtn) {
            toggleContrastBtn.addEventListener('click', () => {
                body.classList.toggle('high-contrast');
                
                if (body.classList.contains('high-contrast')) {
                    localStorage.setItem('highContrast', 'enabled');
                } else {
                    localStorage.removeItem('highContrast');
                }
            });
        }

        // Obsługa zmiany rozmiaru czcionki
        let fontSize = parseFloat(localStorage.getItem('fontSize')) || 1; // Domyślnie 1rem
        body.style.fontSize = `${fontSize}rem`;

        if (increaseFontBtn) {
            increaseFontBtn.addEventListener('click', () => {
                if (fontSize < 3) { // Maksymalny rozmiar czcionki: 3rem
                    fontSize += 0.1;
                    body.style.fontSize = `${fontSize}rem`;
                    localStorage.setItem('fontSize', fontSize);
                }
            });
        }

        if (decreaseFontBtn) {
            decreaseFontBtn.addEventListener('click', () => {
                if (fontSize > 1) { // Minimalny rozmiar czcionki: 1rem
                    fontSize -= 0.1;
                    body.style.fontSize = `${fontSize}rem`;
                    localStorage.setItem('fontSize', fontSize);
                }
            });
        }
    });

    $(document).ready(function () {
        let debounceTimeout;

        function updateFavoriteList(url, data = {}) {
            $.ajax({
                url: url,
                method: 'GET',
                data: data,
                success: function (response) {
                    $('#favorite-list').html(response.html);
                },
                error: function () {
                    alert('Wystąpił błąd podczas aktualizacji listy ulubionych!');
                }
            });
        }

        $(document).on('click', '#reset-filters', function () {
            $('#search').val('');
            $('#category').val('');
            $('#format').val('');
            $('#sort').val('');
            const url = '{{ route("favorites.index") }}';
            const data = $('#filter-form').serialize();
            updateFavoriteList(url, data);
        });

        $(document).on('change', '#filter-form select, #filter-form input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                const url = '{{ route("favorites.index") }}';
                const data = $('#filter-form').serialize();
                updateFavoriteList(url, data);
            }, 300);
        });

        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const data = $('#filter-form').serialize();
            updateFavoriteList(url, data);
        });
    });
</script>

</body>
</html>
