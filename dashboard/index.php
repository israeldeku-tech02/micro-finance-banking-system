<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once '../config/database.php';
$conn = db();

$today = date('Y-m-d');

/* =========================
   KPI DATA
========================= */

$total_clients = $conn->query("SELECT COUNT(*) AS c FROM clients")
    ->fetch_assoc()['c'];

/* Deposits today (transactions) */
$deposits_today = $conn->query("
    SELECT IFNULL(SUM(amount),0) AS t 
    FROM transactions 
    WHERE transaction_date = '$today' 
    AND type = 'deposit'
")->fetch_assoc()['t'];

/* Withdrawals today (transactions) */
$withdrawals_today = $conn->query("
    SELECT IFNULL(SUM(amount),0) AS t 
    FROM transactions 
    WHERE transaction_date = '$today' 
    AND type = 'withdrawal'
")->fetch_assoc()['t'];

/* Savings commitment */
$total_savings = $conn->query("
    SELECT IFNULL(SUM(daily_amount),0) AS t 
    FROM savings 
    WHERE status = 'open'
")->fetch_assoc()['t'];

/* =========================
   SAVINGS STATUS
========================= */

$active_plans = $conn->query("
    SELECT COUNT(*) AS c 
    FROM savings 
    WHERE status = 'open'
")->fetch_assoc()['c'];

$completed_plans = $conn->query("
    SELECT COUNT(*) AS c 
    FROM savings 
    WHERE status = 'closed'
")->fetch_assoc()['c'];

$total_plans = $active_plans + $completed_plans;

$maturity_rate = ($total_plans > 0)
    ? round(($completed_plans / $total_plans) * 100, 1)
    : 0;

/* =========================
   OVERVIEW CHART (REAL FLOW)
========================= */

$labels = [];
$deposits = [];
$withdrawals = [];

$res = $conn->query("
    SELECT 
        transaction_date,
        SUM(CASE WHEN type='deposit' THEN amount ELSE 0 END) AS deposit_total,
        SUM(CASE WHEN type='withdrawal' THEN amount ELSE 0 END) AS withdrawal_total
    FROM transactions
    WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY transaction_date
    ORDER BY transaction_date
");

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $labels[] = $row['transaction_date'];
        $deposits[] = $row['deposit_total'];
        $withdrawals[] = $row['withdrawal_total'];
    }
} else {
    $labels = ["No Data Yet"];
    $deposits = [0];
    $withdrawals = [0];
}

/* =========================
   LAYOUT
========================= */

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/topbar.php';
?>

<link rel="stylesheet" href="../assets/css/dashboardindex.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content">

    <!-- WELCOME -->
    <div class="welcome">
        <h2>Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?> 👋</h2>
        <p>Here’s what’s happening with your system today.</p>
    </div>

    <!-- STATS -->
    <div class="stats">

        <div class="card">
            <i class="fa fa-users"></i>
            <div>
                <h3><?= $total_clients ?></h3>
                <p>Total Clients</p>
            </div>
        </div>

        <div class="card">
            <i class="fa fa-arrow-down"></i>
            <div>
                <h3>GH₵ <?= number_format($deposits_today,2) ?></h3>
                <p>Today's Deposits</p>
            </div>
        </div>

        <div class="card">
            <i class="fa fa-arrow-up"></i>
            <div>
                <h3>GH₵ <?= number_format($withdrawals_today,2) ?></h3>
                <p>Today's Withdrawals</p>
            </div>
        </div>

        <div class="card">
            <i class="fa fa-piggy-bank"></i>
            <div>
                <h3>GH₵ <?= number_format($total_savings,2) ?></h3>
                <p>Total Savings Commitment</p>
            </div>
        </div>

    </div>

    <!-- GRID -->
    <div class="grid">

        <!-- OVERVIEW CHART -->
        <div class="box">
            <h3>Financial Overview (Last 7 Days)</h3>
            <canvas id="chart"></canvas>
        </div>

        <!-- SAVINGS PANEL -->
        <div class="box">
            <h3>Daily Savings Plans</h3>

            <div class="plan">
                <span>Active Plans</span>
                <b><?= $active_plans ?></b>
            </div>

            <div class="plan">
                <span>Completed Plans</span>
                <b><?= $completed_plans ?></b>
            </div>

            <div class="plan">
                <span>Maturity Rate</span>
                <b><?= $maturity_rate ?>%</b>
            </div>

        </div>

    </div>

</div>

<script src="../assets/js/dashboard.js"></script>

<script>
initChart(
    <?= json_encode($labels) ?>,
    <?= json_encode($deposits) ?>,
    <?= json_encode($withdrawals) ?>
);
</script>