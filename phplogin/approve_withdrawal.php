<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require './config.php'; // Your database connection file

if (isset($_GET['id'])) {
    $deposit_id = intval($_GET['id']);

    // Begin a transaction
    mysqli_begin_transaction($link);

    try {
        // Fetch the deposit amount and user_id
        $query = "SELECT user_id, amount FROM withdrawals WHERE id = ?";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'i', $deposit_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $user_id, $amount);
        mysqli_stmt_fetch($stmt);

        // Debugging output
        if ($amount === null || $user_id === null) {
            throw new Exception("No amount or user ID found for this withdrawal.");
        }

        mysqli_stmt_close($stmt);

        // Update deposit status to 'completed'
        $update_query = "UPDATE withdrawals SET status = 'completed' WHERE id = ?";
        $stmt = mysqli_prepare($link, $update_query);
        mysqli_stmt_bind_param($stmt, 'i', $deposit_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error updating withdrawal status");
        }
        mysqli_stmt_close($stmt);

        // Update the user's balance
        $balance_update_query = "UPDATE users SET balance = balance - ? WHERE id = ?";
        $stmt = mysqli_prepare($link, $balance_update_query);
        mysqli_stmt_bind_param($stmt, 'di', $amount, $user_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error updating user balance");
        }

        // Check if the balance update was successful
        if (mysqli_affected_rows($link) === 0) {
            throw new Exception("User balance not updated. User ID: $user_id, Amount: $amount");
        }

        // Fetch the new balance
        $balance_query = "SELECT balance FROM users WHERE id = ?";
        $stmt = mysqli_prepare($link, $balance_query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $new_balance);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Update the session with the new balance
        $_SESSION["balance"] = $new_balance;

        // Commit the transaction
        mysqli_commit($link);

        // Redirect with success message
        header("Location: ./admin.php?message=withdrawal approved successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        mysqli_rollback($link);
        header("Location: ./admin.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}

mysqli_close($link);

$link->close();

