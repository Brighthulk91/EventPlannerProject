<?php
require_once 'config.php';
require_once 'helpers.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']); // remember me toggle

    if (!$email || !$password) {
        $errors[] = 'Both fields are required.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => (int)$user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];

                // Remember Me cookie
                if ($remember) {
                    setcookie("remember_email", $email, time() + (86400 * 7), "/");
                } else {
                    setcookie("remember_email", "", time() - 3600, "/");
                }

                redirect('dashboard.php');
            } else {
                $errors[] = 'Invalid credentials.';
            }
        } else {
            $errors[] = 'User not found.';
        }
    }
}

include 'partials/header.php';
?>
<div class="box center-form">
    <h2>Login</h2>

    <?php if ($errors): ?>
        <div class="error">
            <?php foreach ($errors as $err): ?>
                <p><?php echo htmlspecialchars($err); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="email" name="email" placeholder="Email" 
               value="<?php echo htmlspecialchars($_COOKIE['remember_email'] ?? ''); ?>" required>
        <input type="password" name="password" placeholder="Password" required>

        <!-- Centered Modern Toggle -->
        <div style="display:flex;justify-content:center;align-items:center;margin:15px 0;">
            <label class="toggle">
                <input type="checkbox" name="remember" <?php if (!empty($_COOKIE['remember_email'])) echo 'checked'; ?>>
                <span class="slider"></span>
            </label>
            <span style="margin-left:10px;font-weight:500;">Remember Me</span>
        </div>

        <button type="submit">Login</button>
    </form>
</div>

<?php include 'partials/footer.php'; ?>

<style>
/* Toggle switch styling */
.toggle {
  position: relative;
  width: 50px;
  height: 25px;
}
.toggle input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0;
  right: 0; bottom: 0;
  background-color: #ccc;
  border-radius: 25px;
  transition: .4s;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 4px;
  bottom: 3.5px;
  background-color: white;
  border-radius: 50%;
  transition: .4s;
}
.toggle input:checked + .slider {
  background-color: #7e034f;
}
.toggle input:checked + .slider:before {
  transform: translateX(24px);
}
</style>
