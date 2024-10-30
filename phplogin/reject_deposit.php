<?php
// reject_deposit.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
require_once "./config.php";

// Check if ID is set
if (isset($_GET['id'])) {
    $deposit_id = intval($_GET['id']);

    // Update deposit status to 'rejected'
    $update_query = "UPDATE deposits SET status = 'rejected' WHERE id = ?";
    $stmt = mysqli_prepare($link, $update_query);
    mysqli_stmt_bind_param($stmt, 'i', $deposit_id);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect with success message
        header("Location: ./admin.php?message=Deposit rejected successfully");
    } else {
        // Redirect with error message
        header("Location: ./admin.php?error=Error rejecting deposit");
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($link);
