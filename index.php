<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Prison Management System</title>
</head>
<body>
    <div class="background">
        <nav class="navbar">
            <ul class="nav-list">
                <div class="logo"><img src="https://media.istockphoto.com/id/1298992680/photo/handcuffs-on-wooden-background.jpg?s=612x612&w=0&k=20&c=90pIbShh28S_UZLxhHXMsbJCAZZSd9henhE07p5sTDA=" alt="Logo">
                </div>
                <li><a href="prison_module.php">Prison</a></li>
            <li><a href="visitor_module.php">Visitor</a></li>
            <li><a href="user_module.php">User</a></li>
            <li><a href="inmate_module.php">Inmate</a></li>
            <!-- <li><a href="jail_info.php">Jail Information</a></li> -->
            <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <div class="container">
                    <p class="para"></p>
        </div>
    </div>
    <div class="text">
        <h3 class="function">Functionlities of Prison Management System</h3>
        <section class="user">
            <h3>User Management</h3>
                <ul>
                    <li>Create New users.</li>
                    <li>Assign user roles and permissions.</li>
                    <li>Manage user accounts.</li>
                </ul>
        </section>
        <section class="inmate">
            <h3>Inmate Management</h3>
                <ul>
                    <li>Add new inmate.</li>
                    <li>View inmate details.</li>
                    <li>Edit inmate's information.</li>
                    <li>Delete inmate's record..</li>
                </ul>
        </section>
        <section class="prison">
            <h3>Prison Management</h3>
                <ul>
                    <li>Manage Prison facilities.</li>
                    <li>Defines Overall summary of Prison.</li>
                </ul>
        </section>
        <section class="visitor">
            <h3>Visitor Management</h3>
            <ul>
                <li>View Visitor records.</li>
                <li>Manage visitor permissions.</li>
                <li>Schedule timing for inmate visits.</li>
            </ul>
        </section>
    </div>    
    <footer>
        <p class="text-footer">
            @ Copyright 2023 DG1-005 Priison Management System. All rights are reserved.
        </p>
    </footer>
    <script src="Home.js"></script>
</body>
</html>