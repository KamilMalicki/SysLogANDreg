<?php
/**
 * @license Apache License 2.0
 *
 * Copyright 2025 Kamil Malicki
 *
 * Skrypt aktywacji konta użytkownika na podstawie tokena.
 * Sprawdza, czy token jest poprawny i konto nie jest aktywne.
 * Jeśli wszystko OK – aktywuje konto i usuwa token.
 */

session_start(); // Rozpoczęcie sesji użytkownika

require_once '../inc/db.php'; // Dołączenie pliku z połączeniem do bazy danych ($conn)

if (!isset($_GET['token'])) {
    die('Brak tokenu aktywacyjnego.'); // Jeśli token nie jest przesłany, przerwij działanie
}

$token = $_GET['token']; // Pobranie tokena z parametru GET

// Przygotowanie zapytania do wyszukania użytkownika z podanym tokenem i limitem 1 rekord
$stmt = $conn->prepare("SELECT id, is_active FROM users WHERE activation_token = ? LIMIT 1");
$stmt->bind_param('s', $token); // Podpięcie tokena jako string
$stmt->execute(); // Wykonanie zapytania
$result = $stmt->get_result(); // Pobranie wyniku zapytania

if ($result->num_rows === 0) {
    die('Nieprawidłowy lub wygasły token aktywacyjny.'); // Jeśli brak użytkownika z takim tokenem, zakończ
}

$user = $result->fetch_assoc(); // Pobranie danych użytkownika

if ($user['is_active']) {
    die('Konto jest już aktywne. Możesz się zalogować.'); // Jeśli konto już aktywne, zakończ działanie
}

// Przygotowanie zapytania do aktywacji konta i usunięcia tokena
$stmt = $conn->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?");
$stmt->bind_param('i', $user['id']); // Podpięcie id użytkownika jako integer

if ($stmt->execute()) {
    // Jeśli aktualizacja się powiodła, wyświetl komunikat z linkiem do logowania
    echo "Konto aktywowane pomyślnie. <a href='../frontend/login.html'>Zaloguj się</a>.";
} else {
    // Jeśli aktualizacja się nie powiodła, wyświetl komunikat o błędzie
    echo "Błąd podczas aktywacji konta. Spróbuj ponownie.";
}
?>