<?php
# Include connection
require_once "./config.php";

# Define variables and initialize with empty values
$username_err = $email_err = $password_err = $referral_code_err = "";
$username = $email = $password = $referral_code = "";

# Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST["username"]);
  $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
  $password = trim($_POST["password"]);
  $referral_code = trim($_POST["referral_code"]);

  # Validate username
  if (empty($username)) {
    $username_err = "Please enter a username.";
  } elseif (!ctype_alnum(str_replace(['@', '-', '_'], '', $username))) {
    $username_err = "Username can only contain letters, numbers, and '@', '_', or '-'.";
  } else {
    $sql = "SELECT id FROM users WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "s", $username);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      if (mysqli_stmt_num_rows($stmt) == 1) {
        $username_err = "This username is already registered.";
      }
      mysqli_stmt_close($stmt);
    }
  }

  # Validate email
  if (empty($email)) {
    $email_err = "Please enter an email address.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $email_err = "Please enter a valid email address.";
  } else {
    $sql = "SELECT id FROM users WHERE email = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "s", $email);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      if (mysqli_stmt_num_rows($stmt) == 1) {
        $email_err = "This email is already registered.";
      }
      mysqli_stmt_close($stmt);
    }
  }

  # Validate password
  if (empty($password)) {
    $password_err = "Please enter a password.";
  } elseif (strlen($password) < 8) {
    $password_err = "Password must be at least 8 characters.";
  }

  # Validate referral code (if entered)
  $referred_by = null;  // Default: no referral
  if (!empty($referral_code)) {
    $sql = "SELECT id FROM users WHERE referral_code = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "s", $referral_code);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      if (mysqli_stmt_num_rows($stmt) == 1) {
        $referred_by = $referral_code;  // Valid referral
      } else {
        $referral_code_err = "Invalid referral code.";
      }
      mysqli_stmt_close($stmt);
    }
  }

  # Insert user into database if no errors
  if (empty($username_err) && empty($email_err) && empty($password_err) && empty($referral_code_err)) {
    $sql = "INSERT INTO users (username, email, password, referral_code, referred_by) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $user_referral_code = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);

      mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $hashed_password, $user_referral_code, $referred_by);

      if (mysqli_stmt_execute($stmt)) {
        echo "<script>window.location.href = './login.php';</script>";
        exit;
      } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
      }
      mysqli_stmt_close($stmt);
    }
  }

  # Close connection
  mysqli_close($link);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User login system</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="shortcut icon" href="./img/favicon-16x16.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/form.css">
  <script defer src="./js/script.js"></script>
</head>

<body>
  <div class="container">
    <div class="row min-vh-100 justify-content-center align-items-center">
      <div class="col-lg-5">
        <div class="form-wrap border border-secondary rounded p-4">
          <h1>Sign up</h1>
          <p>Please fill this form to register</p>
          <!-- form starts here -->
          <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" name="username" id="username" value="<?= $username; ?>">
              <small class="text-danger"><?= $username_err; ?></small>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" name="email" id="email" value="<?= $email; ?>">
              <small class="text-danger"><?= $email_err; ?></small>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" name="password" id="password" value="<?= $password; ?>">
              <small class="text-danger"><?= $password_err; ?></small>
            </div>
         <div class="mb-3">
  <label for="referral_code" class="form-label">Referral Code (Optional)</label>
  <input type="text" class="form-control" name="referral_code" id="referral_code" value="<?= isset($referral_code) ? $referral_code : ''; ?>">
          <small class="text-danger"><?= $referral_code_err ?? ''; ?></small>
        </div>

            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="togglePassword">
              <label for="togglePassword" class="form-check-label">Show Password</label>
            </div>
            <div class="mb-3">
              <input type="submit" class="btn " name="submit" value="Sign Up">
            </div>
            <p class="mb-0">Already have an account ? <a href="./login.php"class="text-muted">Log In</a></p>
          </form>
          <!-- form ends here -->
        </div>
      </div>
    </div>
  </div>
  <script>
  const togglePassword = document.querySelector('#togglePassword');
  const passwordField = document.querySelector('#password');

  togglePassword.addEventListener('click', function() {
    // Toggle the type attribute
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
  });
</script>

</body>

</html>