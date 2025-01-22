<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja Zakończona Sukcesem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/accessibility.css') }}">
    <script>
        // Automatyczne przekierowanie po 5 sekundach
        setTimeout(function () {
            window.location.href = "{{ route('products.index') }}";
        }, 5000);
    </script>
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

    <div class="container mt-5 text-center">
        <h1 class="text-success mb-4">Rejestracja Zakończona Sukcesem!</h1>
        <p class="mb-4">Twoje konto zostało utworzone. Za chwilę zostaniesz przekierowany na listę produktów.</p>
        <p class="text-muted">(Jeśli nie nastąpi przekierowanie, kliknij poniższy przycisk.)</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">Przejdź do Produktów</a>
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
