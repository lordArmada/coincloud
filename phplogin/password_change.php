<?php
// Initialize session
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ./login.php");
    exit;
}

require_once "config.php"; // Include the database connection

$current_password_err = $new_password_err = $confirm_password_err = $success_msg = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate current password
    if (empty(trim($_POST["current_password"]))) {
        $current_password_err = "Please enter your current password.";
    } else {
        $current_password = trim($_POST["current_password"]);
    }

    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter a new password.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Passwords did not match.";
        }
    }
    // Check for errors before updating password
    if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
        // Get the current user's data
        $user_id = $_SESSION["id"];

        // Prepare a select statement to check current password
        $sql = "SELECT password FROM users WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind the user ID
            mysqli_stmt_bind_param($stmt, "i", $user_id);

            // Execute the query
            if (mysqli_stmt_execute($stmt)) {
                // Store the result
                mysqli_stmt_store_result($stmt);

                // Check if the user exists, then verify the current password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($current_password, $hashed_password)) {
                            // Current password is correct, update the new password
                            $sql_update = "UPDATE users SET password = ? WHERE id = ?";

                            if ($stmt_update = mysqli_prepare($link, $sql_update)) {
                                // Hash the new password
                                $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

                                // Bind the new password and user ID
                                mysqli_stmt_bind_param($stmt_update, "si", $new_password_hashed, $user_id);

                                // Execute the update query
                                if (mysqli_stmt_execute($stmt_update)) {
                                    $success_msg = "Password successfully changed!";
                                } else {
                                    echo "Oops! Something went wrong. Please try again later.";
                                }

                                mysqli_stmt_close($stmt_update);
                            }
                        } else {
                            $current_password_err = "The current password you entered was not valid.";
                        }
                    }
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
$balance = $_SESSION["balance"] ?? 0;
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
    <li><a href="./referrals.php"><i class="fa-solid fa-people-arrows"></i>Referrals</a></li>
    <li><a href="./userinfo.php"><i class="fa-solid fa-id-badge"></i>Profile</a></li>
        <li><a href="./support.php"><i class="fa-solid fa-info"></i>
                        Support</a></li>
    <li><a href="./password_change.php"  class="active"><i class="fa-solid fa-unlock"></i> Change Password</a></li>
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


             <div class="container mt-5">
        <h2>Change Password</h2>
        <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success"><?php echo $success_msg; ?></div>
                <?php endif; ?>
                <form action="./password_change.php" method="post">
                    <div class="form-group mb-3">
                        <label>Current Password</label>
                        <input type="password" name="current_password"
                            class="form-control <?php echo (!empty($current_password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $current_password_err; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label>New Password</label>
                        <input type="password" name="new_password"
                            class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password"
                            class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Submit">
                    </div>
                </form>
            </div>

            </div>
        </div>
    </div>


      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

    <!-- Pluglin Link -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.0.0/apexcharts.min.js"></script>
    <script src="./js/app.js"></script>
    </body>
    </html>