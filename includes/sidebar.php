<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <div class="sidebar-header">
        <div class="logo-box">
            <i class="fas fa-university"></i>
        </div>

        <div class="logo-text">
            <h2>Durwell</h2>
            <span>Savings & Loans</span>
        </div>
    </div>


    <div class="sidebar-menu">

        <small class="menu-title">MAIN</small>

        <a class="<?=($current=="index.php")?'active':'';?>" href="index.php">
            <i class="fas fa-home"></i>
            Dashboard
        </a>

        <!--<a class="<?=($current=="profile.php")?'active':'';?>" href="profile.php">
            <i class="far fa-user"></i>
            Profile
        </a> -->


        <small class="menu-title">
            CUSTOMER MANAGEMENT
        </small>

        <a class="<?=($current=="clients.php")?'active':'';?>" href="clients.php">
            <i class="fas fa-users"></i>
            Clients
        </a>


        <small class="menu-title">
            SAVINGS
        </small>

        <a class="<?=($current=="transactions.php")?'active':'';?>" href="transactions.php">
            <i class="fas fa-wallet"></i>
            Normal Savings
        </a>

        <a class="<?=($current=="add_savings_account.php")?'active':'';?>" href="add_savings_account.php">
            <i class="fas fa-user-plus"></i>
            Savings Account
        </a>

        <a class="<?=($current=="savings.php")?'active':'';?>" href="savings.php">
            <i class="far fa-calendar-alt"></i>
            Daily Savings
        </a>


        <small class="menu-title">
            TRANSACTIONS
        </small>

        <a class="<?=($current=="reports.php")?'active':'';?>" href="reports.php">
            <i class="fas fa-chart-bar"></i>
            Reports
        </a>


        <small class="menu-title">
            ADMINISTRATION
        </small>

        <a class="<?=($current=="users.php")?'active':'';?>" href="users.php">
            <i class="fas fa-user-cog"></i>
            User Management
        </a>

        <a class="<?=($current=="audit_log.php")?'active':'';?>" href="audit_log.php">
            <i class="fas fa-shield-alt"></i>
            Audit Log
        </a>

        <a class="<?=($current=="settings.php")?'active':'';?>" href="settings.php">
            <i class="fas fa-cog"></i>
            Settings
        </a>

    </div>


    <div class="logout-box">
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>

</div>