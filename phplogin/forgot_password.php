<?php
require_once "./config.php"; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!empty($email)) {
        // Prepare a reset token and expiry
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Update token and expiry in the database
        $sql = "UPDATE users SET password_reset_token = ?, token_expiry = ? WHERE email = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $email);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                // Send reset link via email
                $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";
                $subject = "Password Reset Request";
                $message = "Click the link below to reset your password:\n$reset_link";

                mail($email, $subject, $message, "From: support@yourwebsite.com");

                $success = "A password reset link has been sent to your email.";
            } else {
                $error = "No account found with that email.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $error = "Please enter your email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
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

</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg--secondary text-white text-center">
                    <h3 class="text-white">Forgot Password</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger"><?= $error; ?></div>
                    <?php } elseif (!empty($success)) { ?>
                        <div class="alert alert-success"><?= $success; ?></div>
                    <?php } ?>

                    <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">Enter your email address</label>
                            <input type="email" name="email" class="form-control" id="email"
                                placeholder="example@domain.com" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="./login.php" class="text-muted">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>