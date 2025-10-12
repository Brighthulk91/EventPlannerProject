<?php
require_once 'config.php';
require_once 'helpers.php';

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (!$username || !$email || !$password || !$role) {
        $errors[] = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email.';
    } else {
        // check duplicate email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows) {
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)");
            $insert->bind_param("ssss", $username, $email, $hash, $role);
            if ($insert->execute()) {
                $success = " Registration complete! You can now <a href='login.php'>login</a>.";
            } else {
                $errors[] = "Registration failed. Try again.";
            }
        }
    }
}

include 'partials/header.php';
?>
<div class="box center-form">
    <h2>Register</h2>

    <?php if ($success): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="error">
            <?php foreach ($errors as $err): ?>
                <p><?php echo htmlspecialchars($err); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="manager">Event Manager</option>
            <option value="guest">Guest</option>
        </select>
        <button type="submit">Register</button>
    </form>
</div>

<?php include 'partials/footer.php'; ?>
