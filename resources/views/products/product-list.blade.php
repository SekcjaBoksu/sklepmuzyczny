<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Produktów</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/accessibility.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        #reset-filters {
            width: auto;
            padding: 0.375rem 0.75rem;
            font-size: 1.25rem;
        }
    </style>
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
                        <a class="nav-link active" aria-current="page" href="{{ route('products.index') }}">Strona główna</a>
                    </li>
                    @auth
                        @if(Auth::user()->role === 'employee' || Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('worker.orders.index') }}">Panel Pracownika</a>
                            </li>
                        @endif
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.users.index') }}">Panel Administratora</a>
                            </li>
                        @endif
                        @if(Auth::user()->role === 'employee' || Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('inventory.index') }}">Magazyn</a>
                            </li>
                        @endif
                    @endauth
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
                                    <a class="dropdown-item" href="{{ route('favorites.index') }}">Ulubione ❤︎</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('orders.index') }}">Moje Zamówienia</a>
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
            <button id="increase-font" class="btn btn-outline-secondary btn-sm">+ Czcionka</button>
            <button id="decrease-font" class="btn btn-outline-secondary btn-sm">- Czcionka</button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <!-- Główna Treść -->
    <div class="container mt-5">
        <h1 class="text-center mb-4">Dostępne Produkty</h1>

        <!-- Filtry i Sortowanie -->
        <form id="filter-form" class="row row-cols-lg-auto g-3 align-items-center justify-content-center mb-4">
            <div class="col">
                <label for="search" class="visually-hidden">Szukaj</label>
                <input type="text" id="search" name="search" class="form-control form-control-sm" 
                    placeholder="Szukaj" value="{{ request('search', '') }}">
            </div>
            <div class="col">
                <label for="category" class="visually-hidden">Gatunek</label>
                <select name="category" class="form-select form-select-sm" id="category">
                    <option value="">Wszystkie Gatunki</option>
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
                    <option value="">Wszystkie nośniki</option>
                    <option value="CD" {{ request('format') == 'CD' ? 'selected' : '' }}>CD</option>
                    <option value="Vinyl" {{ request('format') == 'Vinyl' ? 'selected' : '' }}>Vinyl</option>
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
                <button type="button" id="reset-filters" class="btn btn-outline-secondary btn-sm" aria-label="Resetuj filtry">↺</button>
            </div>
        </form>

        <!-- Lista Produktów -->
        <div id="product-list" class="row">
            @include('products.partials.product-list', ['products' => $products])
        </div>
    </div>

    <!-- Skrypty -->
    <script src="{{ asset('js/accessibility.js') }}"></script>
    <script>
        $(document).ready(function () {
            let debounceTimeout;

            function updateProductList(url, data = {}) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: data,
                    success: function (response) {
                        $('#product-list').html(response.html);
                    },
                    error: function () {
                        alert('Wystąpił błąd podczas aktualizacji listy produktów!');
                    }
                });
            }

            // Resetuj filtry
            $(document).on('click', '#reset-filters', function () {
                $('#search').val('');
                $('#category').val('');
                $('#format').val('');
                $('#sort').val('');
                const url = '{{ route("products.index") }}';
                const data = $('#filter-form').serialize();
                updateProductList(url, data);
            });

            $(document).on('change', '#filter-form select, #filter-form input', function () {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    const url = '{{ route("products.filter") }}';
                    const data = $('#filter-form').serialize();
                    updateProductList(url, data);
                }, 300);
            });

            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const data = $('#filter-form').serialize();
                updateProductList(url, data);
            });
        });
    </script>
</body>
</html>
