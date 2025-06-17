<?php
require 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);

    $errors = [];
    if (strlen($first_name) < 3) $errors[] = "Vārds jābūt vismaz 3 simbolus garam.";
    if (strlen($last_name) < 3) $errors[] = "Uzvārds jābūt vismaz 3 simbolus garam.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Nepareizs e-pasta formāts.";

    // Pārbauda vai epasts jau reģistrēts
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = "E-pasts jau ir reģistrēts.";

    if (strlen($password) < 8) $errors[] = "Parole jābūt vismaz 8 simbolus garai.";

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $role = 'user';
        $registered_date = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, registered_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $password_hash, $role, $registered_date);
        $stmt->execute();
        echo "<div class='success-msg'>Reģistrācija veiksmīga. <a href='login.php'>Ielogoties</a></div>";
        exit;
    }
}
include 'includes/header.php';
?>

<form method="post" class="auth-form instagram-form">
    <h2>Reģistrēties</h2>

    <?php
    if (!empty($errors)) {
        echo '<div class="error-msg">';
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo '</div>';
    }
    ?>

    <input type="text" name="first_name" placeholder="Vārds" required minlength="3" />
    <input type="text" name="last_name" placeholder="Uzvārds" required minlength="3" />
    <input type="email" name="email" placeholder="E-pasts" required />
    <input type="password" name="password" placeholder="Parole" required minlength="8" />
    <button type="submit">Reģistrēties</button>
</form>

<?php include 'includes/footer.php'; ?>
