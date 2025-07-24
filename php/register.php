<?php
/**
 * @license Apache License 2.0
 *
 * Copyright 2025 Kamil Malicki
 *
 * Skrypt rejestracji nowego użytkownika.
 * Waliduje email i hasło, sprawdza czy email już istnieje,
 * hashuje hasło, generuje token aktywacyjny,
 * dodaje użytkownika do bazy i wyświetla link aktywacyjny.
 */

require '../inc/db.php'; // Dołączenie połączenia z bazą ($conn)

$email = $_POST['email'] ?? ''; // Pobranie emaila z formularza lub pusty string
$password = $_POST['password'] ?? ''; // Pobranie hasła z formularza lub pusty string

// Walidacja poprawności adresu email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Nieprawidłowy email.');
}

// Sprawdzenie długości hasła (min. 8 znaków)
if (strlen($password) < 8) {
    die('Hasło musi mieć min. 8 znaków.');
}

// Sprawdzenie, czy email jest już zarejestrowany w bazie
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die('Email już zarejestrowany.');
}

// Haszowanie hasła bezpiecznym algorytmem
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Generowanie losowego tokena aktywacyjnego (64 znaki hex)
$token = bin2hex(random_bytes(32));

// Dodanie nowego użytkownika do bazy z tokenem aktywacyjnym
$stmt = $conn->prepare("INSERT INTO users (email, password, activation_token) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $hashed, $token);
$stmt->execute();

// Wyświetlenie komunikatu i linku do aktywacji konta
echo "<p>Konto utworzone! Kliknij, aby aktywować:</p>";
echo "<a href='../php/confirm.php?token=$token'>Aktywuj konto</a>";
?>