<?php


// If user is not logged in, redirect them to login page
// Initialize the session
session_start();
require_once "./config.php"; // Database connection file

// If user is not logged in, redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
    header("Location: ./login.php");
    exit;
}

// Define variables and initialize with empty values
$name = $email = $address = $phone = $id_proof = $bank_account = "";
$name_err = $email_err = $address_err = $phone_err = $id_err = $bank_err = $update_success = "";

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Query to retrieve user data
$sql = "SELECT username, email, address, phone, id_proof, bank_account FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_id);
    $param_id = $user_id;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $name, $email, $address, $phone, $id_proof, $bank_account);
        mysqli_stmt_fetch($stmt);
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    mysqli_stmt_close($stmt);
}

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name, email, address, phone, and bank account
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your full name.";
    } else {
        $name = trim($_POST["name"]);
    }

    if (empty(trim($_POST["email"])) || !filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter your address.";
    } else {
        $address = trim($_POST["address"]);
    }

    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }

    if (empty(trim($_POST["bank_account"]))) {
        $bank_err = "Please enter your bank account or crypto wallet details.";
    } else {
        $bank_account = trim($_POST["bank_account"]);
    }

    // Validate file upload (ID Proof)
    if (empty($_FILES['id_proof']['name'])) {
        $id_err = "Please upload a valid ID document.";
    } else {
        // Set the directory for file uploads
        $target_dir = "./uploads/";
        $id_proof = basename($_FILES['id_proof']['name']);
        $target_file = $target_dir . $id_proof;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is a valid image type
        $check = getimagesize($_FILES["id_proof"]["tmp_name"]);
        if ($check === false) {
            $id_err = "File is not an image.";
        } elseif (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $id_err = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif ($_FILES["id_proof"]["size"] > 2000000) {  // 2MB file size limit
            $id_err = "Your file is too large.";
        } elseif (!move_uploaded_file($_FILES['id_proof']['tmp_name'], $target_file)) {
            $id_err = "Sorry, there was an error uploading your file.";
        }
    }

    // If no errors, proceed to update the database
    if (empty($name_err) && empty($email_err) && empty($address_err) && empty($phone_err) && empty($id_err) && empty($bank_err)) {
        $sql = "UPDATE users SET username = ?, email = ?, address = ?, phone = ?, id_proof = ?, bank_account = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $name, $email, $address, $phone, $id_proof, $bank_account, $user_id);

            if (mysqli_stmt_execute($stmt)) {
                $update_success = "KYC profile updated successfully!";
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
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
         #file-label {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
        margin-right: 10px;
    }

    #file-label:hover {
        background-color: #45a049;
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
    <li><a href="./index.php"><i class="fa-solid fa-chart-simple"></i> Dashboard</a></li>
    <li><a href="./transaction.php"><i class="fa-solid fa-money-bill-transfer"></i>Transactions</a></li>
    <li><a href="./referrals.php"><i class="fa-solid fa-people-arrows"></i>Referrals</a></li>
    <li><a href="./userinfo.php"  class="active"><i class="fa-solid fa-id-badge"></i>Profile</a></li>
        <li><a href="./support.php" ><i class="fa-solid fa-info"></i>
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
        <h2>KYC Profile</h2>
        <p>Complete your KYC and withdrawal information below:</p>

        <?php if (!empty($update_success)): ?>
            <div class="alert alert-success">
                <?php echo $update_success; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
            enctype="multipart/form-data">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name"
                    class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($name); ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                    class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($email); ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address"
                    class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($address); ?>">
                <span class="invalid-feedback"><?php echo $address_err; ?></span>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone"
                    class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($phone); ?>">
                <span class="invalid-feedback"><?php echo $phone_err; ?></span>
            </div>

            <div class="form-group">
    <label>ID Proof (Upload)</label>
    <?php if (!empty($id_proof)): ?>
                    <p>Uploaded File: <?php echo htmlspecialchars($id_proof); ?></p>
                    <input type="file" name="id_proof" class="form-control">
                <?php else: ?>
                    <input type="file" name="id_proof" class="form-control <?php echo (!empty($id_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $id_err; ?></span>
                <?php endif; ?>
            </div>


            <div class="form-group">
                <label>Bank Account / Crypto Wallet</label>
                <input type="text" name="bank_account"
                    class="form-control <?php echo (!empty($bank_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo htmlspecialchars($bank_account); ?>">
                <span class="invalid-feedback"><?php echo $bank_err; ?></span>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update KYC</button>
            </div>
        </form>
    </div>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

    <!-- Pluglin Link -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.0.0/apexcharts.min.js"></script>
    <script src="./js/app.js"></script>
<script>
    document.getElementById("file-label").textContent = "File uploaded successfully!";
</script>
</body>

</html>