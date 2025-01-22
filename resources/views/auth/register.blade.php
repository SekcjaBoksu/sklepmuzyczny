<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/accessibility.css') }}">
</head>
<body>
    <!-- Przyciski dostępności -->
    <div class="container mt-2">
        <div class="d-flex gap-2 justify-content-end">
            <button id="toggle-contrast" class="btn btn-outline-secondary btn-sm">Tryb wysoki kontrast</button>
            <button id="increase-font" class="btn btn-outline-secondary btn-sm">Powiększ czcionkę</button>
            <button id="decrease-font" class="btn btn-outline-secondary btn-sm">Pomniejsz czcionkę</button>
        </div>
    </div>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Rejestracja</h1>
        
        <!-- Wiadomość o sukcesie -->
        @if(session('success'))
            <div class="alert alert-success" role="alert" aria-live="polite">
                {{ session('success') }}
            </div>
        @endif

        <!-- Wiadomości o błędach -->
        @if($errors->any())
            <div class="alert alert-danger" role="alert" aria-live="assertive">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formularz rejestracji -->
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Login <span class="text-danger">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    class="form-control" 
                    value="{{ old('name') }}" 
                    autocomplete="username" 
                    required 
                    aria-label="Wprowadź swój login, maksymalnie 255 znaków"
                >
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Adres Email <span class="text-danger">*</span></label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    class="form-control" 
                    value="{{ old('email') }}" 
                    autocomplete="email" 
                    required 
                    aria-label="Wprowadź poprawny adres email"
                >
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Hasło <span class="text-danger">*</span></label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    class="form-control" 
                    autocomplete="new-password" 
                    required 
                    pattern=".{8,}" 
                    title="Hasło musi mieć przynajmniej 8 znaków"
                    aria-label="Wprowadź hasło o długości co najmniej 8 znaków"
                >
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Potwierdź Hasło <span class="text-danger">*</span></label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation" 
                    class="form-control" 
                    autocomplete="new-password" 
                    required 
                    aria-label="Powtórz swoje hasło dla potwierdzenia"
                >
            </div>
            <button 
                type="submit" 
                class="btn btn-primary w-100 mb-3" 
                aria-label="Kliknij, aby zarejestrować nowe konto"
            >
                Zarejestruj się
            </button>
        </form>

        <!-- Powrót do strony głównej -->
        <div class="text-center">
            <a 
                href="{{ route('products.index') }}" 
                class="btn btn-secondary w-50" 
                aria-label="Powrót do strony głównej"
            >
                Powrót do Strony Głównej
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const toggleContrastBtn = document.getElementById('toggle-contrast');
            const increaseFontBtn = document.getElementById('increase-font');
            const decreaseFontBtn = document.getElementById('decrease-font');

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

            // Zmiana rozmiaru czcionki
            let fontSize = parseFloat(localStorage.getItem('fontSize')) || 1;
            body.style.fontSize = `${fontSize}rem`;

            if (increaseFontBtn) {
                increaseFontBtn.addEventListener('click', () => {
                    if (fontSize < 3) { // Maksymalny rozmiar czcionki: 3x
                        fontSize += 0.1;
                        body.style.fontSize = `${fontSize}rem`;
                        localStorage.setItem('fontSize', fontSize);
                    }
                });
            }

            if (decreaseFontBtn) {
                decreaseFontBtn.addEventListener('click', () => {
                    if (fontSize > 1) { // Minimalny rozmiar czcionki: 1x
                        fontSize -= 0.1;
                        body.style.fontSize = `${fontSize}rem`;
                        localStorage.setItem('fontSize', fontSize);
                    }
                });
            }
        });
    </script>
</body>
</html>
