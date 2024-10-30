
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
# Initialize the session
session_start();
require_once "./config.php";

# If user is not logged in then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    echo "<script>" . "window.location.href='./login.php';" . "</script>";
    exit;
}
$balance = $_SESSION["balance"] ?? 0;
$sql = "SELECT balance FROM users WHERE id = ?";
if ($stmt = $link->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();
    $stmt->close();
}
$sql = "SELECT referral_code FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_id);
    $param_id = $user_id;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $referral_code);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);
}
function generateReferralCode($user_id)
{
    return strtoupper(md5($user_id . time()));  // Example using MD5 and timestamp for uniqueness
}

// If the user doesn't have a referral code, generate one
if (empty($referral_code)) {
    $referral_code = generateReferralCode($user_id);
    $sql = "UPDATE users SET referral_code = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $referral_code, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Generate the referral link
$referral_link = "http://localhost/website/coincloud.com/phplogin/register.php?ref=" . urlencode($referral_code);
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coincloud-</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link href="/midas home page/css/8014c396.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/main.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@1,400;1,500&family=Maven+Pro:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="/your-path-to-fontawesome/css/fontawesome.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" />
        

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.css">

    <link rel="shortcut icon" href="./img/favicon-16x16.png" type="image/x-icon">

    <style>
        .pb-120 {
            padding-bottom: clamp(40px, 4vw, 40px);
        }

        .pt-120 {
            padding-top: clamp(40px, 4vw, 40px);
        }

        .container {
            max-width: 1140px;
        }
    </style>

</head>

<body>

    <div class="d-flex flex-wrap">

        <div class="dashboard-sidebar" id="dashboard-sidebar">
            <button class="btn-close dash-sidebar-close d-xl-none"></button>
            <a class="navbar-brand" href="http://127.0.0.1:5500/">
                <i class="fa-solid fa-coins" style="color: #000000;"></i>
                <span class="text">
                    <span class="line wow fadeInRight" data-wow-duration=".6s"
                        data-wow-delay=".6s">Coincloud</span><span class="logo-slogan">Wallet</span>
                </span>
            </a>
            
            <div class="bg--lights">
                <div class="profile-info">
                    <p class="fs--20px  fw-bold">ACCOUNT BALANCE</p>
                    <h4 class="usd-balance text--secondary fs--25px"><?php echo htmlspecialchars($balance) ?></h4>
                    <div class="mt-4 d-flex flex-wrap gap-2">
                        <a href="./deposit.php" class="btn btn--secondary btn--smd" id="deposit">Deposit</a>
                        <a href="./withdraw.php"
                            class="btn btn--secondary btn--smd">Withdraw</a>
                    </div>
                </div>
            </div>
     <ul class="sidebar-menu">
    <li><a href="./index.php" ><i class="fa-solid fa-chart-simple"></i> Dashboard</a></li>
    <li><a href="./transaction.php"><i class="fa-solid fa-money-bill-transfer"></i>Transactions</a></li>
    <li><a href="./referrals.php"  class="active"><i class="fa-solid fa-people-arrows"></i>Referrals</a></li>
    <li><a href="./userinfo.php"><i class="fa-solid fa-id-badge"></i>Profile</a></li>
        <li><a href="./support.php"><i class="fa-solid fa-info"></i>
                        Support</a></li>
    <li><a href="./password_change.php"><i class="fa-solid fa-unlock"></i> Change Password</a></li>
    <li><a href="./logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logout</a></li>
</ul>
      
        </div>

        <div class="dashboard-wrapper">

            <div class="dashboard-nav d-flex flex-wrap align-items-center justify-content-between">
                <div class="nav-left d-flex gap-4 align-items-center">
                    <div class="dash-sidebar-toggler d-xl-none" id="dash-sidebar-toggler">
                        <i class="fas fa-bars"></i>
                    </div>
                </div>
                <div class="nav-right d-flex flex-wrap align-items-center gap-3">
                    <select name="langSel" class="langSel form--control h-auto px-2 py-1 border-0">
                        <option value="en" selected>English</option>
                    </select>
                    <ul class="nav-header-link d-flex flex-wrap gap-2">
                        <li>
                            <a class="link " style="background: rgb(41, 56, 96);" href="javascript:void(0)"><i
                                    class="fa-solid fa-user fa-lg" style="color: white;"></i></a>
                            <div class="dropdown-wrapper">
                                <div class="dropdown-header">
                                    <h6 class="name text--secondary">WELCOME </h6>
                                    <p class="fs--20px"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                </div>
                                <ul class="links">
                                    <li><a href="./userinfo.php"><i
                                                class="las la-user"></i> Profile</a></li>
                                    <li><a href="./password_change.php"><i
                                                class="las la-key"></i> Change Password</a></li>
                                    <li><a href="./logout.php"><i class="las la-sign-out-alt"></i> Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
              <div class="container">
        <h2>Referral Program</h2>
        <p>Invite your friends and earn rewards when they sign up!</p>

        <div class="form-group">
            <label>Your Referral Link:</label>
            <input type="text" class="form-control" value="<?php echo $referral_link; ?>" readonly>
                    <button class="btn btn-primary mt-2" onclick="copyReferral()">Copy Link</button>
                </div>
            
                <h4>Your Referrals</h4>
                <?php
                // Fetch referred users for the logged-in user
                $sql = "SELECT u.username, r.referral_date FROM users u INNER JOIN referrals r ON u.id = r.referred_user_id WHERE r.user_id = ?";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $param_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $referred_username, $referral_date);

                    echo "<ul>";
                    while (mysqli_stmt_fetch($stmt)) {
                        echo "<li>" . htmlspecialchars($referred_username) . " (Referred on: " . $referral_date . ")</li>";
                    }
                    echo "</ul>";
                    mysqli_stmt_close($stmt);
                }
                ?>
            </div>
            
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

    <!-- Pluglin Link -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.0.0/apexcharts.min.js"></script>
    <script src="./js/app.js"></script>
            <script>
                function copyReferral() {
                    var copyText = document.querySelector("input[type='text']");
                    copyText.select();
                    document.execCommand("copy");
                    alert("Referral link copied to clipboard!");
                }
            </script>