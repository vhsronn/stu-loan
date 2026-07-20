<?php
// Database helper for frontend-only screens.
// NOTE: This project is still missing the backend migration/schema in the repo.
// These helpers assume tables exist:
// - students(id, name, email, address)
// - loans(id, student_id, amount, loan_type, status)
// - payments(id, loan_id, amount, payment_date, payment_method)

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;

    $pdo = new PDO(
        'mysql:host=db;dbname=testdb;charset=utf8mb4',
        'user',
        'pass',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    return $pdo;
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

