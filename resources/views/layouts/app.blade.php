<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sklep Muzyczny')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRn5jBoE5F8Z4FFx9AB8QF2CTdffCUaGQdxyDd8mY" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/accessibility.css') }}">
    <script src="{{ asset('js/accessibility.js') }}" defer></script>
</head>
<body>
    <!-- Pasek nawigacyjny -->
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
                    @if(Auth::check() && (Auth::user()->role === 'employee' || Auth::user()->role === 'admin'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('inventory') ? 'active' : '' }}" href="{{ route('inventory.index') }}">Magazyn</a>
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
            <button id="increase-font" class="btn btn-outline-secondary btn-sm">+ Powiększ czcionkę</button>
            <button id="decrease-font" class="btn btn-outline-secondary btn-sm">- Pomniejsz czcionkę</button>
        </div>
    </div>

    <!-- Główna treść -->
    <div class="container mt-4">
        @yield('content')
    </div>
</body>
</html>
