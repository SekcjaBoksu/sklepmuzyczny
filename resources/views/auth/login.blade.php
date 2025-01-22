<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
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
        <h1 class="text-center mb-4">Logowanie</h1>

        <!-- Wiadomość o błędzie -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Adres Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Hasło</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Zaloguj się</button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-secondary w-50">Powrót do Strony Głównej</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const toggleContrastBtn = document.getElementById('toggle-contrast');
            const increaseFontBtn = document.getElementById('increase-font');
            const decreaseFontBtn = document.getElementById('decrease-font');

            // Obsługa trybu wysokiego kontrastu
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
            let fontSize = parseFloat(localStorage.getItem('fontSize')) || 1;
            body.style.fontSize = `${fontSize}rem`;

            if (increaseFontBtn) {
                increaseFontBtn.addEventListener('click', () => {
                    if (fontSize < 3) { // Maksymalna wielkość: 3x
                        fontSize += 0.1;
                        body.style.fontSize = `${fontSize}rem`;
                        localStorage.setItem('fontSize', fontSize);
                    }
                });
            }

            if (decreaseFontBtn) {
                decreaseFontBtn.addEventListener('click', () => {
                    if (fontSize > 1) { // Minimalna wielkość: 1x (domyślna)
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
