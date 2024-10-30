<?php
// Initialize the session
session_start();
require_once "./config.php"; // Database connection file

// If user is not logged in, redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    header("Location: ./login.php");
    exit;
}

// Get the logged-in user's ID
$user_id = $_SESSION["id"];
$balance = $_SESSION["balance"] ?? 0;
// Fetch deposit history from the database for the logged-in user
$sql = "SELECT gateway, amount, status, created_at FROM deposits WHERE user_id = ? ORDER BY created_at DESC";
if ($stmt = $link->prepare($sql)) {
    // Bind the user ID to the query
    $stmt->bind_param("i", $user_id);

    // Execute the query
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Fetch all records
    $deposits = $result->fetch_all(MYSQLI_ASSOC);

    // Close statement
    $stmt->close();
}

// Close connection
$link->close();
?>

<!-- meta tags and other links -->
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
                .table th {
            background-color: rgb(41, 56, 96);
            color: white;
        }
        .table td, .table th {
            vertical-align: middle;
              border-bottom: 1px solid #ddd;
        }
        .status-pending {
            color: orange;
        }
        .status-completed {
            color: green;
        }
        .status-failed {
            color: red;
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
    <li><a href="./index.php" class="active"><i class="fa-solid fa-chart-simple"></i> Dashboard</a></li>
    <li><a href="./transaction.php"><i class="fa-solid fa-money-bill-transfer"></i>Transactions</a></li>
    <li><a href="./referrals.php"><i class="fa-solid fa-people-arrows"></i>Referrals</a></li>
    <li><a href="./userinfo.php"><i class="fa-solid fa-id-badge"></i>Profile</a></li>
      <li><a href="./support.php"<i class="fa-solid fa-info"></i>
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

<div class="container mt-5">
    <h2 class="mb-4">Deposit History</h2>

    <!-- Check if deposits exist -->
    <?php if (!empty($deposits)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="text-center table-secondary">
                    <tr>
                        <th>Date</th>
                        <th>Gateway</th>
                        <th>Amount (USD)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php foreach ($deposits as $deposit): ?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($deposit['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($deposit['gateway']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($deposit['amount'], 2)); ?></td>
                            <td>
                                <?php
                                $status = htmlspecialchars($deposit['status']);
                                if ($status == 'pending') {
                                    echo '<span class="status-pending">Pending</span>';
                                } elseif ($status == 'completed') {
                                    echo '<span class="status-completed">Completed</span>';
                                } elseif ($status == 'failed') {
                                    echo '<span class="status-failed">Failed</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center">No deposit history found.</p>
    <?php endif; ?>
</div>



            </div>
        </div>
    </div>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

            <!-- Pluglin Link -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
            <script
                src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.0.0/apexcharts.min.js"></script>
            <script src="./js/app.js"></script>
</body>






</body>

</html>