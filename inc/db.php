<?php
/**
 * @license Apache License 2.0
 *
 * Copyright 2025 Kamil Malicki
 *
 * Plik odpowiedzialny za połączenie z bazą danych MySQL.
 * Używa rozszerzenia mysqli, ustawia kodowanie UTF-8.
 * W razie błędu połączenia skrypt zakończy działanie z komunikatem.
 */

$host = 'localhost';  // Adres serwera bazy danych
$user = 'root';       // Nazwa użytkownika bazy danych
$pass = '';           // Hasło użytkownika bazy danych
$db = 'auth_db';      // Nazwa bazy danych

$conn = new mysqli($host, $user, $pass, $db); // Nawiązanie połączenia z bazą

if ($conn->connect_error) {
    // Jeśli połączenie nieudane, zakończ skrypt z komunikatem o błędzie
    die("Błąd połączenia z bazą: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4"); // Ustawienie kodowania na UTF-8 (pełne wsparcie znaków Unicode)
