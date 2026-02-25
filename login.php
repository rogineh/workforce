<?php
// login.php
require_once 'includes/db.php';
session_start();

$error = '';

// Auto-seed admin user if none exists (for development ease)
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $username = 'admin';
        $password = 'admin123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
        $stmt->execute([$username, $hash]);
    }
} catch (Exception $e) {
    // Table might not exist yet if they haven't run schema.sql
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workforce System - Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        .login-card {
            background: white;
            padding: 3rem;
            border-radius: 1.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .login-card h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #0f172a;
            font-size: 1.5rem;
        }

        .login-card .logo {
            color: var(--accent);
            text-align: center;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .error-msg {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="logo">Workforce</div>
        <h1>Sign In to Portal</h1>

        <?php if ($error): ?>
            <div class="error-msg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="admin">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" name="login" class="btn btn-primary"
                style="width: 100%; padding: 1rem; margin-top: 1rem;">Login</button>
        </form>
        <p style="text-align: center; margin-top: 2rem; color: var(--text-muted); font-size: 0.75rem;">
            Default login: admin / admin123
        </p>
    </div>
</body>

</html>