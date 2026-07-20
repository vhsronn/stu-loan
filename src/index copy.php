<?php
// Simple front controller to keep the UI navigation clean.
// Use ?page=students | loans | payments

$page = $_GET['page'] ?? 'students';
$studentId = $_GET['student_id'] ?? null;
$loanId = $_GET['loan_id'] ?? null;

switch ($page) {
  case 'loans':
    require __DIR__ . '/loans.php';
    break;
  case 'payments':
    require __DIR__ . '/payments.php';
    break;
  case 'students':
  default:
    require __DIR__ . '/students.php';
    break;
}

