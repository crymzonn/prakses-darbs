<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mājaslapa</title>
    <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
<header class="nav">
    <div class="nav-container">
        <a href="index.php" class="logo">Mājaslapa
        <nav>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php">Sākums</a>
            <a href="profile.php">Profils</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') echo '<a href="admin.php">Admin</a>'; ?>
            <a href="logout.php" class="logout-btn">Iziet</a>
        <?php else: ?>
            <a href="login.php">Ielogoties</a>
            <a href="register.php">Reģistrēties</a>
        <?php endif; ?>
        </nav>
    </div>
</header>
<main>
