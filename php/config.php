<?php
/**
 * @license Apache License 2.0
 *
 * Copyright 2025 Kamil Malicki
 *
 * Skrypt aktywacji konta użytkownika na podstawie tokena aktywacyjnego.
 * Sprawdza token z parametru GET, weryfikuje go w bazie, 
 * a następnie aktywuje konto i usuwa token.
 */

require '../inc/db.php'; // Dołączenie pliku z połączeniem do bazy danych

$token = $_GET['token'] ?? ''; // Pobranie tokena z parametru GET, jeśli brak - pusty string

if (!$token) {
    die('Brak tokena.'); // Jeśli token nie został przesłany, przerwij skrypt
}

$stmt = $conn->prepare("SELECT id FROM users WHERE activation_token = ?"); // Przygotowanie zapytania wyszukującego użytkownika z danym tokenem
$stmt->bind_param("s", $token); // Podpięcie tokena do zapytania (typ string)
$stmt->execute(); // Wykonanie zapytania
$result = $stmt->get_result(); // Pobranie wyniku zapytania

if ($result->num_rows === 0) {
    die('Nieprawidłowy token.'); // Jeśli nie znaleziono użytkownika z takim tokenem, zakończ działanie
}

$user = $result->fetch_assoc(); // Pobranie danych użytkownika (w tym id)

$stmt = $conn->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?"); 
// Przygotowanie zapytania do aktywacji konta i usunięcia tokena

$stmt->bind_param("i", $user['id']); // Podpięcie id użytkownika do zapytania (typ integer)
$stmt->execute(); // Wykonanie aktualizacji

echo "✅ Konto aktywowane. Możesz się teraz zalogować."; // Komunikat o sukcesie
?>