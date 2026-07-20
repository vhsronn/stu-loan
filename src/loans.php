<?php
require_once __DIR__ . '/layout.php';

$pdo = db();

$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
if ($studentId <= 0) {
    beginPage('Loans');
    echo "<p class='muted'>Missing or invalid student_id.</p>";
    endPage();
    exit;
}

// Fetch student (optional, for display)
$studentName = null;
try {
    $stmt = $pdo->prepare('SELECT id, name FROM students WHERE id = ?');
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();
    $studentName = $student ? $student['name'] : null;
} catch (Throwable $ex) {
    $studentName = null;
}

// Create loan (frontend-only: submit form; assumes loans table exists)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_loan'])) {
    $amount = (float)($_POST['amount'] ?? 0);
    $loanType = $_POST['loan_type'] ?? 'Tuition';
    $status = $_POST['status'] ?? 'Pending';

    try {
        $stmt = $pdo->prepare('INSERT INTO loans (student_id, amount, loan_type, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$studentId, $amount, $loanType, $status]);
        header('Location: index.php?page=loans&student_id=' . $studentId);
        exit;
    } catch (Throwable $ex) {
        $error = $ex->getMessage();
    }
}

$loans = [];
try {
    $stmt = $pdo->prepare('SELECT id, amount, loan_type, status FROM loans WHERE student_id = ? ORDER BY id DESC');
    $stmt->execute([$studentId]);
    $loans = $stmt->fetchAll();
} catch (Throwable $ex) {
    $loans = [];
}

beginPage('Loans');

$headline = 'Loans for Student #' . $studentId;
if ($studentName) $headline .= ' - ' . $studentName;

echo "<h1>" . e($headline) . "</h1>";

echo "<div class='row'>";

card('Add Loan');
if (!empty($error)) {
    echo "<p style='color:#b00020'>" . e($error) . "</p>";
}

echo "<form method='post'>";
echo "<input type='hidden' name='create_loan' value='1'>";

echo "<label>Amount</label><br><input required name='amount' type='number' step='0.01' min='0' value='0'>";

echo "<br><br><label>Loan Type</label><br>";
echo "<select required name='loan_type'>";
echo "<option>Tuition</option><option>Books</option><option>Living Expenses</option>";
echo "</select>";

echo "<br><br><label>Status</label><br>";
echo "<select required name='status'>";
echo "<option>Pending</option><option>Approved</option><option>Disbursed</option>";
echo "</select>";

echo "<br><br><button type='submit'>Save Loan</button>";

echo "</form>";
endCard();

card('Loans List');

echo "<table><thead><tr>";
echo "<th>ID</th><th>Amount</th><th>Loan Type</th><th>Status</th><th class='actions'>Action</th>";
echo "</tr></thead><tbody>";

if (!$loans) {
    echo "<tr><td colspan='5' class='muted'>No loans found for this student.</td></tr>";
} else {
    foreach ($loans as $l) {
        $lid = $l['id'];
        echo "<tr>";
        echo "<td>" . e((string)$l['id']) . "</td>";
        echo "<td>" . e((string)$l['amount']) . "</td>";
        echo "<td>" . e((string)$l['loan_type']) . "</td>";
        echo "<td>" . e((string)$l['status']) . "</td>";
        echo "<td class='actions'>";
echo "<a href='index.php?page=payments&loan_id=" . e((string)$lid) . "'><button type='button'>View</button></a>";
        echo "</td>";
        echo "</tr>";
    }
}

echo "</tbody></table>";
endCard();

echo "</div>";

endPage();

