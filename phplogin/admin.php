<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require './config.php'; // Your database connection file

// Fetch data for dashboard statistics
// Fetch Total Users
$total_users_query = "SELECT COUNT(*) as total_users FROM users";
$total_users_result = mysqli_query($link, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result)['total_users'];

// Fetch Total Deposits
$total_deposits_query = "SELECT SUM(amount) as total_deposits FROM deposits WHERE status = 'completed'";
$total_deposits_result = mysqli_query($link, $total_deposits_query);
$total_deposits = mysqli_fetch_assoc($total_deposits_result)['total_deposits'] ?? 0;

// Fetch Total Withdrawals
$total_withdrawals_query = "SELECT SUM(amount) as total_withdrawals FROM withdrawals WHERE status = 'completed'";
$total_withdrawals_result = mysqli_query($link, $total_withdrawals_query);
$total_withdrawals = mysqli_fetch_assoc($total_withdrawals_result)['total_withdrawals'] ?? 0;

// Fetch users for Manage Users section
$users_query = "SELECT id, username, email, balance FROM users";
$users_result = mysqli_query($link, $users_query);

// Fetch deposits for Manage Deposits section
$deposits_query = "SELECT * FROM deposits WHERE status = 'pending'";
$deposits_result = mysqli_query($link, $deposits_query);

// Fetch withdrawals for Manage Withdrawals section
$withdrawals_query = "SELECT * FROM withdrawals WHERE status = 'pending'";
$withdrawals_result = mysqli_query($link, $withdrawals_query);

// Fetch transaction history using a combined query
 // Replace this with the appropriate user ID or get it from a session or parameter
$query = "
    SELECT 'Deposit' AS type,d.id, d.user_id, d.amount, d.status, d.created_at 
    FROM deposits d 
    
    UNION ALL
    
    SELECT 'Withdrawal' AS type,w.id, w.user_id, w.amount, w.status, w.created_at 
    FROM withdrawals w 
    
    UNION ALL
    
    SELECT t.type, t.user_id,t.id, t.amount, t.status, t.created_at 
    FROM transactions t 
    
    ORDER BY created_at DESC
";

// Execute the query directly without parameters
$transactions_result = mysqli_query($link, $query);

// Check for errors in query execution
if (!$transactions_result) {
    die("Query Failed: " . mysqli_error($link));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #wrapper {
            display: flex;
            align-items: stretch;
        }

        #sidebar-wrapper {
            width: 250px;
            background-color: #343a40;
            color: #ffffff;
            min-height: 100vh;
        }

        .sidebar-heading {
            font-size: 1.5rem;
            padding: 1rem;
            background-color: #495057;
        }

        .list-group-item {
            background-color: #343a40;
            color: white;
        }

        .list-group-item:hover {
            background-color: #495057;
            color: white;
        }

        .list-group-item.active {
            background-color: #007bff;
            color: white;
        }

        #page-content-wrapper {
            flex: 1;
            padding: 2rem;
        }
    </style>
</head>

<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark" id="sidebar-wrapper">
            <div class="sidebar-heading">Admin Dashboard</div>
            <div class="list-group list-group-flush">
                <a href="#dashboard" class="list-group-item list-group-item-action bg-dark">Dashboard</a>
                <a href="#users" class="list-group-item list-group-item-action bg-dark">Manage Users</a>
                <a href="#deposits" class="list-group-item list-group-item-action bg-dark">Manage Deposits</a>
                <a href="#withdrawals" class="list-group-item list-group-item-action bg-dark">Manage Withdrawals</a>
                <a href="#transactions" class="list-group-item list-group-item-action bg-dark">Transactions History</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <button class="btn btn-primary" id="menu-toggle">Toggle Menu</button>
            </nav>

            <div class="container-fluid">
                <h1 class="mt-4">Admin Dashboard</h1>

                <!-- Dashboard Overview -->
                <div id="dashboard">
                    <h3>Overview</h3>
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-white bg-secondary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Users</h5>
                                    <p class="card-text" id="total-users"><?php echo $total_users; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Deposits</h5>
                                    <p class="card-text" id="total-deposits">
                                        $<?php echo number_format($total_deposits, 2); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Withdrawals</h5>
                                    <p class="card-text" id="total-withdrawals">
                                        $<?php echo number_format($total_withdrawals, 2); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manage Users -->
                <div id="users" class="mt-4">
                    <h3>Manage Users</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Balance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($users_result)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo number_format($row['balance'], 2); ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <a href="delete_user.php?id=<?php echo $row['id']; ?>"
                                            class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Manage Deposits -->
                <div id="deposits" class="mt-4">
        <div id="manage-deposits" class="mt-4">
    <h3>Manage Deposits</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Deposit ID</th>
                <th>User ID</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($deposits_result)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['user_id']; ?></td>
                            <td>$<?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <?php if ($row['status'] == 'pending'): ?>
                                    <!-- Approve Button -->
                                    <a href="./approve_deposit.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-success btn-sm">Approve</a>
                                    <!-- Reject Button -->
                                    <a href="./reject_deposit.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                                <?php else: ?>
                                    <span class="text-muted">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
                <!-- Manage Withdrawals -->
                <div id="withdrawals" class="mt-4">
                    <h3>Manage Withdrawals</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Withdrawal ID</th>
                                <th>User ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($withdrawals_result)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['user_id']; ?></td>
                                    <td>$<?php echo number_format($row['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <a href="./approve_withdrawal.php?id=<?php echo $row['id']; ?>"
                                            class="btn btn-success btn-sm">Approve</a>
                                        <a href="./reject_withdrawal.php?id=<?php echo $row['id']; ?>"
                                            class="btn btn-danger btn-sm">Reject</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Transactions History -->
                <div id="transactions" class="mt-4">
        <h3>Transactions History</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>User ID</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($transactions_result)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['user_id']; ?></td>
                                    <td>$<?php echo number_format($row['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($transactions_result) == 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No transactions found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                </div>
                
                <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
                </body>
                
                </html>
                
                <?php
                // Free results and close connection
                mysqli_free_result($total_users_result);
                mysqli_free_result($total_deposits_result);
                mysqli_free_result($total_withdrawals_result);
                mysqli_free_result($users_result);
                mysqli_free_result($deposits_result);
                mysqli_free_result($withdrawals_result);
                mysqli_free_result($transactions_result);
                mysqli_close($link);
                ?>