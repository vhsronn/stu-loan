<?php
require_once __DIR__ . '/layout.php';

$pdo = db();

$loanId = isset($_GET['loan_id']) ? (int)$_GET['loan_id'] : 0;
if ($loanId <= 0) {
    beginPage('Payments');
    echo "<p class='muted'>Missing or invalid loan_id.</p>";
    endPage();
    exit;
}

// Fetch loan details (optional)
$loan = null;
try {
    $stmt = $pdo->prepare('SELECT id, student_id, amount, loan_type, status FROM loans WHERE id = ?');
    $stmt->execute([$loanId]);
    $loan = $stmt->fetch();
} catch (Throwable $ex) {
    $loan = null;
}

// Create payment (frontend-only: submit form; assumes payments table exists)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_payment'])) {
    $amount = (float)($_POST['amount'] ?? 0);
    $paymentDate = $_POST['payment_date'] ?? date('Y-m-d');
    $method = $_POST['payment_method'] ?? 'Cash';

    try {
        $stmt = $pdo->prepare('INSERT INTO payments (loan_id, amount, payment_date, payment_method) VALUES (?, ?, ?, ?)');
        $stmt->execute([$loanId, $amount, $paymentDate, $method]);
        header('Location: index.php?page=payments&loan_id=' . $loanId);
        exit;
    } catch (Throwable $ex) {
        $error = $ex->getMessage();
    }
}

// Fetch payments + computed totals
$payments = [];
$totalPaid = 0.0;
try {
    $stmt = $pdo->prepare('SELECT id, amount, payment_date, payment_method FROM payments WHERE loan_id = ? ORDER BY id DESC');
    $stmt->execute([$loanId]);
    $payments = $stmt->fetchAll();

    $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount),0) AS total_paid FROM payments WHERE loan_id = ?');
    $stmt->execute([$loanId]);
    $totalPaid = (float)($stmt->fetch()['total_paid'] ?? 0);
} catch (Throwable $ex) {
    $payments = [];
    $totalPaid = 0.0;
}

$loanAmount = $loan ? (float)$loan['amount'] : 0.0;
$remaining = max(0, $loanAmount - $totalPaid);

beginPage('Payments');

echo "<h1>Payments for Loan #" . e((string)$loanId) . "</h1>";
if ($loan) {
    echo "<p class='muted'>Loan Amount: <b>" . e((string)$loanAmount) . "</b> | Status: " . e((string)$loan['status']) . "</p>";
}

echo "<div class='row'>";

card('Add Payment');
if (!empty($error)) {
    echo "<p style='color:#b00020'>" . e($error) . "</p>";
}

echo "<form method='post'>";

echo "<input type='hidden' name='create_payment' value='1'>";

echo "<label>Payment Amount</label><br><input required name='amount' type='number' step='0.01' min='0' value='0'>";

echo "<br><br><label>Payment Date</label><br><input required name='payment_date' type='date' value='" . e(date('Y-m-d')) . "'>";

echo "<br><br><label>Payment Method</label><br>";
echo "<select required name='payment_method'>";
echo "<option>Cash</option><option>Bank Transfer</option><option>Online Payment</option>";
echo "</select>";

echo "<br><br><button type='submit'>Save Payment</button>";
echo "</form>";

endCard();

card('Payments List & Totals');
echo "<p class='muted'>Total Paid: <b>" . e(number_format($totalPaid, 2)) . "</b> | Remaining Balance: <b>" . e(number_format($remaining, 2)) . "</b></p>";

echo "<table><thead><tr>";
echo "<th>ID</th><th>Amount</th><th>Payment Date</th><th>Payment Method</th>";
echo "</tr></thead><tbody>";

if (!$payments) {
    echo "<tr><td colspan='4' class='muted'>No payments recorded for this loan.</td></tr>";
} else {
    foreach ($payments as $p) {
        echo "<tr>";
        echo "<td>" . e((string)$p['id']) . "</td>";
        echo "<td>" . e((string)$p['amount']) . "</td>";
        echo "<td>" . e((string)$p['payment_date']) . "</td>";
        echo "<td>" . e((string)$p['payment_method']) . "</td>";
        echo "</tr>";
    }
}

echo "</tbody></table>";
endCard();

echo "</div>";

// Back link
if ($loan && (int)$loan['student_id'] > 0) {
    $sid = (int)$loan['student_id'];
    echo "<p><a href='index.php?page=loans&student_id=" . e((string)$sid) . "'>← Back to Loans</a></p>";
}

endPage();

