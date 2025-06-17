<?php
require 'includes/db.php';
require 'includes/auth.php';

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Iegūstam pašreizējos datus
$stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_first_name = htmlspecialchars(trim($_POST['first_name']));
    $new_last_name = htmlspecialchars(trim($_POST['last_name']));
    $new_email = htmlspecialchars(trim($_POST['email']));
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validācija
    if (strlen($new_first_name) < 3) $errors[] = "Vārds jābūt vismaz 3 simbolus garam.";
    if (strlen($new_last_name) < 3) $errors[] = "Uzvārds jābūt vismaz 3 simbolus garam.";
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Nepareizs e-pasta formāts.";

    // Pārbaudam, vai e-pasts jau nav aizņemts citu lietotāju
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $stmt->bind_param("si", $new_email, $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = "Šāds e-pasts jau ir reģistrēts.";
    $stmt->close();

    // Paroles maiņa, ja parole ievadīta
    if (!empty($new_password) || !empty($confirm_password)) {
        if (strlen($new_password) < 8) {
            $errors[] = "Ja maināt paroli, tā jābūt vismaz 8 simbolus garai.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Jaunā parole un apstiprinājums nesakrīt.";
        }
        // Pārbaudām pašreizējo paroli
        if (empty($current_password)) {
            $errors[] = "Lai mainītu paroli, ievadiet pašreizējo paroli.";
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            $stmt->close();

            if (!password_verify($current_password, $hashed_password)) {
                $errors[] = "Pašreizējā parole ir nepareiza.";
            }
        }
    }

    if (empty($errors)) {
        if (!empty($new_password)) {
            $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ? WHERE user_id = ?");
            $stmt->bind_param("ssssi", $new_first_name, $new_last_name, $new_email, $password_hash, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $new_first_name, $new_last_name, $new_email, $user_id);
        }
        if ($stmt->execute()) {
            $success = "Profils veiksmīgi atjaunināts.";
            // Atjaunojam sesijas datus
            $_SESSION['first_name'] = $new_first_name;
            $_SESSION['last_name'] = $new_last_name;
        } else {
            $errors[] = "Radās kļūda, mēģiniet vēlreiz.";
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<h2>Profila rediģēšana</h2>

<?php
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>$error</p>";
    }
}
if ($success) {
    echo "<p style='color:green;'>$success</p>";
}
?>

<form method="post" class="auth-form">
    <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" placeholder="Vārds" required>
    <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" placeholder="Uzvārds" required>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="E-pasts" required>

    <hr>
    <p>Lai mainītu paroli, aizpildiet zemāk esošos laukus:</p>
    <input type="password" name="current_password" placeholder="Pašreizējā parole">
    <input type="password" name="new_password" placeholder="Jaunā parole">
    <input type="password" name="confirm_password" placeholder="Apstiprināt jauno paroli">

    <button type="submit">Saglabāt izmaiņas</button>
</form>

<?php include 'includes/footer.php'; ?>
