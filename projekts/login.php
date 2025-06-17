<?php
require 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $first_name, $last_name, $hashed_password, $role);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['role'] = $role;
            header("Location: index.php");
            exit;
        } else {
            $error = "Nepareiza parole.";
        }
    } else {
        $error = "E-pasts netika atrasts.";
    }
}
include 'includes/header.php';
?>

<form method="post" class="auth-form instagram-form">
    <h2>Ielogoties</h2>

    <?php
    if (isset($error)) {
        echo '<div class="error-msg"><p>' . $error . '</p></div>';
    }
    ?>

    <input type="email" name="email" placeholder="E-pasts" required />
    <input type="password" name="password" placeholder="Parole" required />
    <button type="submit">Ielogoties</button>
</form>

<?php include 'includes/footer.php'; ?>
