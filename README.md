# Sklep Muzyczny

## Opis projektu
Sklep muzyczny to aplikacja internetowa umożliwiająca zarządzanie produktami muzycznymi, zamówieniami oraz interakcję użytkowników w przyjaznym interfejsie. Projekt wspiera również dostępność dla osób z niepełnosprawnościami.

## Funkcjonalności
1. **Zarządzanie produktami**:
   - Dodawanie, edycja, usuwanie produktów przez adminów i pracowników.
   - Kategorie produktów, formaty (CD, Vinyl, Specjalne Edycje).

2. **Koszyk**:
   - Dodawanie produktów do koszyka.
   - Dynamiczna aktualizacja wartości zamówienia.
   - Możliwość usuwania pozycji z koszyka.

3. **Zarządzanie zamówieniami**:
   - Pracownicy mogą aktualizować status wysyłki.
   - Admini i pracownicy mają dostęp do panelu zarządzania zamówieniami.

4. **Użytkownicy i role**:
   - Role użytkowników (klient, pracownik, admin).
   - Panel admina do zarządzania użytkownikami.

5. **Dostępność**:
   - Tryb wysokiego kontrastu.
   - Elementy dostępne z odpowiednimi opisami ARIA.
   - Przełącznik powiększania tekstu dla osób słabowidzących.

6. **Filtrowanie i sortowanie**:
   - Filtrowanie produktów po kategorii, formacie, i nazwie.
   - Sortowanie według ceny i tytułu.

## Technologie użyte w projekcie
- **Backend**: Laravel 11, PHP 8.2
- **Frontend**: Bootstrap 5, jQuery
- **Baza danych**: MySQL
- **Inne**: FontAwesome, ikony Bootstrap

## Instrukcja instalacji
1. **Klonowanie repozytorium**:
  ```
git clone <repozytorium-url> cd muzyczny-sklep
  ```


3. **Instalacja zależności**:
  ```
composer install npm install
  ```

4. **Konfiguracja środowiska**:
- Skopiuj plik `.env.example` jako `.env`:
  ```
  cp .env.example .env
  ```
- Skonfiguruj połączenie z bazą danych w pliku `.env`.

4. **Migracja bazy danych i jej seedowanie **:
  ```
php artisan migrate
  ```
-Następnie użyj seederów do wygenerowania rekordów
  ```
php artisan db:seed
  ```

5. **Uruchomienie serwera**:
  ```
php artisan serve
  ```
- Następnie przejdź na http://127.0.0.1:8000/


## Dostępność
- **Aria**: Elementy interfejsu wykorzystują etykiety ARIA, zapewniając opis i funkcję kontrolerów.
- **Tryb wysokiego kontrastu**: Podmienia kolory elementów na stronie, na takie z wysokim kontrastem.
- **Powiększanie czcionki**: Powiększa lub pomniejsza czcionkę przyciskami na stronie.

