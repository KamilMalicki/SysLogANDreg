<?php
/**
 * @license Apache License 2.0
 *
 * Copyright 2025 Kamil Malicki
 *
 * Skrypt wylogowania użytkownika.
 * Usuwa wszystkie dane sesji i przekierowuje na stronę logowania.
 */

session_start(); // Rozpoczęcie sesji

session_unset(); // Usunięcie wszystkich zmiennych sesji
session_destroy(); // Zniszczenie sesji

header('Location: ../frontend/login.html'); // Przekierowanie na stronę logowania
exit; // Zakończenie skryptu
?>