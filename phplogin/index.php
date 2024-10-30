<?php
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
    <li><a href="./index.php" class="active"><i class="fa-solid fa-chart-simple"></i> Dashboard</a></li>
    <li><a href="./transaction.php"><i class="fa-solid fa-money-bill-transfer"></i>Transactions</a></li>
    <li><a href="./referrals.php"><i class="fa-solid fa-people-arrows"></i>Referrals</a></li>
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


            <div class=" dashboard-container ">


                <div class="dashboard-inner">






                    <div class="row g-3 mt-4">
                        <div class="col-lg-4">
                            <div class="dashboard-widget" style="  border: 2px solid rgba(41, 56, 96, 1);">
                                <div class="d-flex justify-content-between">
                                    <h5 class="text-secondary">Successful Deposits</h5>
                                </div>
                                <h3 class="text--secondary my-4">0.00 USD</h3>
                                <div class="widget-lists">
                                    <div class="row">
                                        <div class="col-4">
                                            <p class="fw-bold">Submitted</p>
                                            <span>$0.00</span>
                                        </div>
                                        <div class="col-4">
                                            <p class="fw-bold">Pending</p>
                                            <span>$0.00</span>
                                        </div>
                                        <div class="col-4">
                                            <p class="fw-bold">Rejected</p>
                                            <span>$0.00</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <p><small><i>You've requested to deposit $100.00. Where $100.00 is just initiated
                                                but not submitted.</i></small></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="dashboard-widget" style="border: 2px solid rgba(41, 56, 96, 1);">
                                <div class="d-flex justify-content-between">
                                    <h5 class="text-secondary">Successful Withdrawals</h5>
                                </div>
                                <h3 class="text--secondary my-4">0.00 USD</h3>
                                <div class="widget-lists">
                                    <div class="row">
                                        <div class="col-4">
                                            <p class="fw-bold">Submitted</p>
                                            <span>$0.00</span>
                                        </div>
                                        <div class="col-4">
                                            <p class="fw-bold">Pending</p>
                                            <span>$0.00</span>
                                        </div>
                                        <div class="col-4">
                                            <p class="fw-bold">Rejected</p>
                                            <span>$0.00</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <p><small><i>You've requested to withdraw $0.00. Where $0.00 is just initiated but
                                                not submitted.</i></small></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="dashboard-widget" style="border: 2px solid rgba(41, 56, 96, 1);">
                                <div class="d-flex justify-content-between">
                                    <h5 class="text-secondary">Total Investments</h5>
                                </div>
                                <h3 class="text--secondary my-4">0.00 USD</h3>
                                <div class="widget-lists">
                                    <div class="row">
                                        <div class="col-4">
                                            <p class="fw-bold">Completed</p>
                                            <span>$0.00</span>
                                        </div>
                                        <div class="col-4">
                                            <p class="fw-bold">Running</p>
                                            <span>$0.00</span>
                                        </div>
                                        <div class="col-4">
                                            <p class="fw-bold">Interests</p>
                                            <span>$0.00</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <p><small><i>You've invested $0.00 from the deposit wallet and $0.00 from the
                                                interest wallet</i></small></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4 mb-4">
                        <div class="card-body">
                            <div class="mb-2">
                                <h5 class="title">Latest ROI Statistics</h5>
                                <p> <small><i>Here is last 30 days statistics of your ROI (Return on
                                            Investment)</i></small></p>
                            </div>
                            <div id="chart"></div>
                        </div>
                    </div>
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

    <script>

        // apex-line chart
        var options = {
            chart: {
                height: 350,
                type: "area",
                toolbar: {
                    show: false
                },
                dropShadow: {
                    enabled: true,
                    enabledSeries: [0],
                    top: -2,
                    left: 0,
                    blur: 10,
                    opacity: 0.08,
                },
                animations: {
                    enabled: true,
                    easing: 'linear',
                    dynamicAnimation: {
                        speed: 1000
                    }
                },
            },
            dataLabels: {
                enabled: false
            },
            series: [
                {
                    name: "Price",  // Name of the series
                    data: [100, 200, 150, 300, 250, 400, 350]  // Example price data for each month
                }
            ],
            fill: {
                type: "gradient",
                colors: [' #fffff', ' #ffffff', ' #ffffff'],
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.6,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                title: {
                    text: "Months"  // Label for the x-axis
                },
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']  // Time period (months)
            },
            grid: {
                padding: {
                    left: 5,
                    right: 5
                },
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
            },
        };

        // Render the chart using the above options
        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();


        var chart = new ApexCharts(document.querySelector("#chart"), options);

        chart.render();


        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

    </script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>





</body>

</html>