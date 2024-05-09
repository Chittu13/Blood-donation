<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        /* Add your CSS styles here */
        .message { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>Reset Your Password</h1>
        </header>
        <main>
            <?php
            if (isset($_SESSION['error'])) {
                echo "<p class='message'>" . $_SESSION['error'] . "</p>";
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo "<p class='success'>" . $_SESSION['success'] . "</p>";
                unset($_SESSION['success']);
            }
            ?>
            <form action="validate_email.php" method="post">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Submit</button>
            </form>
        </main>
    </div>
</body>
</html>
