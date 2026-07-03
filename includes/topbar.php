<?php
require_once __DIR__.'/../config/database.php';

$conn = db();

$system_name = "Durwell Savings & Loans";

$result = $conn->query("SELECT system_name FROM settings LIMIT 1");

if($result && $row = $result->fetch_assoc()){
    $system_name = $row['system_name'];
}

$username = $_SESSION['username'] ?? "Administrator";
?>

<div class="topbar">

    <div class="top-left">

        <button class="menu-btn">
            <i class="fas fa-bars"></i>
        </button>

        <div class="search-box">

            <i class="fas fa-search"></i>

            <input
                type="text"
                placeholder="Search for clients, accounts, transactions..."
            >

            <span class="shortcut">
                Ctrl + K
            </span>

        </div>

    </div>


    <div class="top-right">

        <div class="icon-btn">
            <i class="far fa-bell"></i>
            <span class="badge">3</span>
        </div>

        <div class="icon-btn">
            <i class="far fa-envelope"></i>
        </div>


        <div class="profile">

            <img src="assets/images/avatar.png" alt="User">

            <div class="profile-info">

                <h4><?=htmlspecialchars($username)?></h4>

                <span>Administrator</span>

            </div>

            <i class="fas fa-chevron-down"></i>

        </div>

    </div>

</div>