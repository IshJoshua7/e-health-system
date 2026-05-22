<?php
session_start();
// Simple demo credentials
$USERS = [
    'admin' => 'password',
];

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['username'], $_POST['password'])
) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    if (isset($USERS[$u]) && $USERS[$u] === $p) {
        $_SESSION['user'] = $u;
        header('Location: /dashboard.php');
        exit;
    }
    $error = 'Invalid credentials';
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title></head>
<body>
<h2>Demo Login</h2>
<?php if (!empty($error)): ?><div style="color:red"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post">
  <label>Username <input name="username" required></label><br>
  <label>Password <input type="password" name="password" required></label><br>
  <button type="submit">Login</button>
</form>
</body>
</html>
