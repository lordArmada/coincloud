<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
require_once "./config.php";

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $withdrawal_id = intval($_GET['id']); // Ensure it's an integer

    if ($withdrawal_id > 0) {
        // Prepare the SQL query to update the status to 'rejected'
        $update_query = "UPDATE withdrawals SET status = 'rejected' WHERE id = ?";

        // Prepare the statement
        if ($stmt = mysqli_prepare($link, $update_query)) {
            // Bind the withdrawal ID to the query
            mysqli_stmt_bind_param($stmt, 'i', $withdrawal_id);

            // Execute the query and check if successful
            if (mysqli_stmt_execute($stmt)) {
                // Success: Redirect with a success message
                header("Location: ./admin.php?message=Withdrawal rejected successfully");
                exit();
            } else {
                // Execution failed: Redirect with an error message
                header("Location: ./admin.php?error=Error rejecting withdrawal");
                exit();
            }

            // Close the statement
        } else {
            // SQL preparation failed
            header("Location: ./admin.php?error=Error preparing SQL statement");
            exit();
        }
    } else {
        // Invalid withdrawal ID
        header("Location: ./admin.php?error=Invalid withdrawal ID");
        exit();
    }
} else {
    // 'id' parameter not found in the URL
    header("Location: ./admin.php?error=No withdrawal ID provided");
    exit();
}

// Close the database connection
mysqli_close($link);
