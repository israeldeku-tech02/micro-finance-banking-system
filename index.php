<?php
// index.php - Login + OTP
session_start();

// If already logged in fully (with OTP)
if (!empty($_SESSION['user_id']) && !empty($_SESSION['otp_verified'])) {
    header('Location: dashboard/index.php');
    exit;
}

$err = $_GET['err'] ?? '';
$step = $_SESSION['otp_pending'] ?? false; // flag to know which form to show
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Durwell Savings & Loan Service - Admin Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
    }

    body{
      font-family:'Segoe UI',Tahoma,Verdana,sans-serif;
      min-height:100vh;
      background:#eef3f9;
    }

    .container{
      display:flex;
      min-height:100vh;
    }

    /* ===== Left Branding Panel ===== */
    .left-panel{
      flex:1;
      position:relative;
      overflow:hidden;
      background:
        linear-gradient(rgba(8,25,63,.90), rgba(8,25,63,.92)),
        url('assets/bank-bg.jpg') center/cover no-repeat;
      color:#fff;
      padding:4rem;
      display:flex;
      flex-direction:column;
      justify-content:center;
      align-items:flex-start;
    }

    .left-panel::before{
      content:'';
      position:absolute;
      width:420px;
      height:420px;
      border-radius:50%;
      background:rgba(212,175,55,.08);
      top:-120px;
      right:-120px;
    }

    .left-panel::after{
      content:'';
      position:absolute;
      width:300px;
      height:300px;
      border-radius:50%;
      background:rgba(255,255,255,.05);
      bottom:-80px;
      left:-80px;
    }

    .brand{
      position:relative;
      z-index:2;
      max-width:560px;
    }

    .brand img{
      width:140px;
      max-width:100%;
      margin-bottom:1.5rem;
    }

    .brand h1{
      font-size:3rem;
      line-height:1.15;
      margin-bottom:1rem;
      font-weight:800;
    }

    .brand .tagline{
      color:#d4af37;
      font-size:1.25rem;
      font-weight:600;
      margin-bottom:1.5rem;
    }

    .brand p{
      font-size:1.05rem;
      line-height:1.8;
      color:#dbe6ff;
      margin-bottom:2rem;
    }

    .features{
      display:flex;
      flex-wrap:wrap;
      gap:1rem;
    }

    .feature{
      background:rgba(255,255,255,.10);
      border:1px solid rgba(255,255,255,.15);
      padding:.9rem 1rem;
      border-radius:14px;
      backdrop-filter:blur(10px);
      font-size:.95rem;
      display:flex;
      align-items:center;
      gap:.6rem;
    }

    .feature i{
      color:#d4af37;
    }

    /* ===== Right Login Panel ===== */
    .right-panel{
      flex:1;
      display:flex;
      justify-content:center;
      align-items:center;
      padding:2rem;
      background:linear-gradient(135deg,#f5f8fc,#eef3f9);
    }

    .login-card{
      width:100%;
      max-width:430px;
      background:rgba(255,255,255,.88);
      border:1px solid rgba(255,255,255,.6);
      backdrop-filter:blur(14px);
      padding:2.6rem;
      border-radius:26px;
      box-shadow:0 25px 60px rgba(0,0,0,.14);
    }

    .card-icon{
      width:88px;
      height:88px;
      border-radius:50%;
      margin:0 auto 1rem;
      display:flex;
      align-items:center;
      justify-content:center;
      background:linear-gradient(135deg,#0b1f4d,#153a75);
      color:#fff;
      font-size:2rem;
      box-shadow:0 12px 25px rgba(11,31,77,.25);
    }

    .login-card h2{
      text-align:center;
      color:#0b1f4d;
      font-size:2rem;
      margin-bottom:.6rem;
      font-weight:800;
    }

    .divider{
      width:80px;
      height:4px;
      border-radius:999px;
      background:#d4af37;
      margin:0 auto 2rem;
    }

    .alert{
      background:#ffe2e2;
      color:#b00020;
      padding:.9rem 1rem;
      border-radius:12px;
      margin-bottom:1rem;
      font-size:.92rem;
      border:1px solid #ffc7c7;
    }

    label{
      display:block;
      margin:1rem 0 .45rem;
      font-weight:700;
      color:#24324a;
      font-size:.95rem;
    }

    .input-group{
      position:relative;
    }

    .input-group .left-icon{
      position:absolute;
      left:15px;
      top:50%;
      transform:translateY(-50%);
      color:#7a869a;
      font-size:1rem;
    }

    .input-group input{
      width:100%;
      padding:1rem 3rem 1rem 2.9rem;
      border:1px solid #d7dfeb;
      border-radius:14px;
      font-size:1rem;
      outline:none;
      transition:.25s ease;
      background:#fff;
    }

    .input-group input:focus{
      border-color:#153a75;
      box-shadow:0 0 0 4px rgba(21,58,117,.12);
    }

    .eye-btn{
      position:absolute;
      right:14px;
      top:50%;
      transform:translateY(-50%);
      background:none;
      border:none;
      color:#7a869a;
      cursor:pointer;
      font-size:1rem;
    }

    .forgot-link{
      display:block;
      text-align:right;
      margin-top:.9rem;
      color:#153a75;
      text-decoration:none;
      font-weight:600;
      font-size:.92rem;
    }

    .forgot-link:hover{
      text-decoration:underline;
    }

    .btn-primary{
      width:100%;
      border:none;
      border-radius:14px;
      padding:1rem;
      margin-top:1.4rem;
      background:linear-gradient(90deg,#0b1f4d,#153a75);
      color:#fff;
      font-size:1rem;
      font-weight:800;
      cursor:pointer;
      transition:.25s ease;
      box-shadow:0 12px 25px rgba(11,31,77,.22);
    }

    .btn-primary:hover{
      transform:translateY(-2px);
      box-shadow:0 16px 30px rgba(11,31,77,.28);
    }

    .btn-primary i{
      margin-right:.5rem;
    }

    .otp-note{
      margin-top:1rem;
      text-align:center;
      font-size:.9rem;
      color:#556070;
    }

    /* ===== Responsive ===== */
    @media (max-width: 991px){
      .container{
        flex-direction:column;
      }

      .left-panel{
        align-items:center;
        text-align:center;
        padding:3rem 2rem;
      }

      .brand{
        max-width:100%;
      }

      .brand h1{
        font-size:2.3rem;
      }

      .features{
        justify-content:center;
      }

      .right-panel{
        padding:2rem 1.2rem 3rem;
      }
    }

    @media (max-width: 575px){
      .left-panel{
        padding:2.5rem 1.2rem;
      }

      .brand img{
        width:110px;
      }

      .brand h1{
        font-size:1.9rem;
      }

      .brand .tagline{
        font-size:1rem;
      }

      .brand p{
        font-size:.95rem;
      }

      .login-card{
        padding:2rem 1.3rem;
        border-radius:22px;
      }

      .login-card h2{
        font-size:1.7rem;
      }
    }
  </style>
</head>
<body>

  <div class="container">

    <!-- Left Branding -->
    <div class="left-panel">
      <div class="brand">
        <img src="assets/logo1.png" alt="Durwell Logo">

        <h1>Durwell Savings &amp; Loan Service</h1>

        <div class="tagline">Secure. Reliable. Trusted Financial Services.</div>

        <p>
          Manage customer accounts, structured savings, loan portfolios,
          repayments, and financial operations through a secure
          administrative portal built for modern microfinance management.
        </p>

        <div class="features">
          <div class="feature"><i class="fa-solid fa-shield-halved"></i> Secure Access</div>
          <div class="feature"><i class="fa-solid fa-wallet"></i> Savings Management</div>
          <div class="feature"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Services</div>
          <div class="feature"><i class="fa-solid fa-chart-line"></i> Financial Reports</div>
        </div>
      </div>
    </div>

    <!-- Right Login -->
    <div class="right-panel">
      <div class="login-card">

        <?php if (!$step): ?>

          <div class="card-icon">
            <i class="fa-solid fa-lock"></i>
          </div>

          <h2>Admin Login</h2>
          <div class="divider"></div>

          <?php if ($err): ?>
            <div class="alert"><?= htmlspecialchars($err) ?></div>
          <?php endif; ?>

          <form method="post" action="backend/login.php" autocomplete="off">

            <label>Username</label>
            <div class="input-group">
              <i class="fa-regular fa-user left-icon"></i>
              <input type="text" name="username" placeholder="Enter username" required autofocus>
            </div>

            <label>Password</label>
            <div class="input-group">
              <i class="fa-solid fa-lock left-icon"></i>
              <input type="password" id="password" name="password" placeholder="Enter password" required>
              <button type="button" class="eye-btn" id="togglePwd">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>

            <a href="#" class="forgot-link">Forgot Password?</a>

            <button type="submit" class="btn-primary">
              <i class="fa-solid fa-right-to-bracket"></i>
              Login Securely
            </button>
          </form>

        <?php else: ?>

          <div class="card-icon">
            <i class="fa-solid fa-key"></i>
          </div>

          <h2>Enter OTP</h2>
          <div class="divider"></div>

          <?php if ($err): ?>
            <div class="alert"><?= htmlspecialchars($err) ?></div>
          <?php endif; ?>

          <form method="post" action="backend/verify_otp.php" autocomplete="off">

            <label>One-Time Password (OTP)</label>

            <div class="input-group">
              <i class="fa-solid fa-key left-icon"></i>
              <input type="text" name="otp" placeholder="Enter the OTP" required autofocus>
            </div>

            <button type="submit" class="btn-primary">
              <i class="fa-solid fa-circle-check"></i>
              Verify OTP
            </button>
          </form>

          <p class="otp-note">
            (For demo, OTP is <strong><?= $_SESSION['otp_code'] ?? '' ?></strong>)
          </p>

        <?php endif; ?>

      </div>
    </div>

  </div>

  <script>
    const togglePwd = document.getElementById("togglePwd");
    const pwdField = document.getElementById("password");

    if (togglePwd) {
      togglePwd.addEventListener("click", () => {
        if (pwdField.type === "password") {
          pwdField.type = "text";
          togglePwd.innerHTML = '<i class="fa-regular fa-eye-slash"></i>';
        } else {
          pwdField.type = "password";
          togglePwd.innerHTML = '<i class="fa-regular fa-eye"></i>';
        }
      });
    }
  </script>

</body>
</html>
