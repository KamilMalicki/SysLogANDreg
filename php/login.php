<?php
/**
 * @license Apache License 2.0
 *
 * Copyright 2025 Kamil Malicki
 *
 * Skrypt logowania użytkownika.
 * Sprawdza poprawność metody żądania, waliduje dane, weryfikuje użytkownika w bazie.
 * Ustawia sesję dla zalogowanego użytkownika i przekierowuje do panelu.
 */

session_start(); // Rozpoczęcie sesji

require '../inc/db.php'; // Dołączenie połączenia z bazą ($conn)

// Sprawdzenie, czy żądanie jest metodą POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/login.html'); // Jeśli nie, przekieruj na formularz logowania
    exit;
}

$email = $_POST['email'] ?? ''; // Pobranie emaila z formularza lub pusty string
$password = $_POST['password'] ?? ''; // Pobranie hasła z formularza lub pusty string

// Walidacja adresu email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Nieprawidłowy email.');
}

// Sprawdzenie, czy hasło nie jest puste
if (empty($password)) {
    die('Podaj hasło.');
}

// Przygotowanie zapytania SQL do pobrania użytkownika po emailu
$stmt = $conn->prepare("SELECT id, password, is_active FROM users WHERE email = ?");
if (!$stmt) {
    die('Błąd przygotowania zapytania.');
}

$stmt->bind_param("s", $email); // Podpięcie emaila jako string
$stmt->execute(); // Wykonanie zapytania
$result = $stmt->get_result(); // Pobranie wyniku

if (!$result) {
    die('Błąd zapytania.');
}

$user = $result->fetch_assoc(); // Pobranie danych użytkownika

// Sprawdzenie, czy użytkownik istnieje i hasło jest poprawne
if (!$user || !password_verify($password, $user['password'])) {
    die('Błędny email lub hasło.');
}

// Sprawdzenie, czy konto jest aktywne
if (!$user['is_active']) {
    die('Konto nieaktywne. Sprawdź link aktywacyjny.');
}

$_SESSION['user_id'] = $user['id']; // Ustawienie ID użytkownika w sesji

header('Location: ../index.php'); // Przekierowanie do panelu użytkownika
exit;
?>