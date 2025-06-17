<?php
require 'includes/auth.php';
include 'includes/header.php';
?>

<h1>Laipni, <?php echo htmlspecialchars($_SESSION['first_name']) . ' ' . htmlspecialchars($_SESSION['last_name']); ?>!</h1>
<p>Šī ir tava sākumlapa.</p>

<?php include 'includes/footer.php'; ?>
