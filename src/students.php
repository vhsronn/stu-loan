<?php
require_once __DIR__ . '/layout.php';

$pdo = db();

$students = [];
try {
    $stmt = $pdo->query('SELECT id, name, email, address FROM students ORDER BY id DESC');
    $students = $stmt->fetchAll();
} catch (Throwable $ex) {
    // If DB schema isn't created yet, still render the UI shell.
    $students = [];
}

beginPage('Students - CRUD');

card('Student List');

echo "<table><thead><tr>";
echo "<th>ID</th><th>Name</th><th>Email</th><th>Address</th><th class='actions'>Action</th>";
echo "</tr></thead><tbody>";

if (!$students) {
    echo "<tr><td colspan='5' class='muted'>No students found. Create students in phpMyAdmin first, or implement students CRUD.</td></tr>";
} else {
    foreach ($students as $s) {
        $sid = $s['id'];
        echo "<tr>";
        echo "<td>" . e((string)$s['id']) . "</td>";
        echo "<td>" . e((string)$s['name']) . "</td>";
        echo "<td>" . e((string)$s['email']) . "</td>";
        echo "<td>" . e((string)$s['address']) . "</td>";
        echo "<td class='actions'>";
        echo "<a href='index.php?page=loans&student_id=" . e((string)$sid) . "'><button type='button'>Loans</button></a> ";
        echo "</td>";
        echo "</tr>";
    }
}

echo "</tbody></table>";

endCard();

endPage();

