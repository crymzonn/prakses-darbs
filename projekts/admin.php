<?php
require 'includes/db.php';
require 'includes/auth.php';

// Pārbaudām, vai lietotājs ir admins
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Dzēšanas loģika
if (isset($_GET['delete_user'])) {
    $delete_user_id = intval($_GET['delete_user']);
    // Neļaujam adminam dzēst sevi
    if ($delete_user_id !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $delete_user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php");
        exit;
    }
}

// Iegūstam visus lietotājus
$result = $conn->query("SELECT user_id, first_name, last_name, email, registered_date, role FROM users ORDER BY user_id ASC");

include 'includes/header.php';
?>

<h2>Admina panelis - Lietotāju pārvaldība</h2>

<table border="1" cellpadding="10" cellspacing="0" style="width:100%; max-width:800px; margin:auto;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Vārds</th>
            <th>Uzvārds</th>
            <th>E-pasts</th>
            <th>Reģistrēšanās datums</th>
            <th>Loma</th>
            <th>Darbības</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $user['user_id']; ?></td>
            <td><?php echo htmlspecialchars($user['first_name']); ?></td>
            <td><?php echo htmlspecialchars($user['last_name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo $user['registered_date']; ?></td>
            <td><?php echo $user['role']; ?></td>
            <td>
                <?php if ($user['user_id'] !== $_SESSION['user_id']): ?>
                    <a href="admin.php?delete_user=<?php echo $user['user_id']; ?>" onclick="return confirm('Vai tiešām vēlaties dzēst šo lietotāju?');" style="color:red;">Dzēst</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
