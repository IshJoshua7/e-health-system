<?php
session_start();
if (empty($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}
?><!doctype html>
<html>
<head><meta charset="utf-8"><title>Dashboard</title></head>
<body>
<h2>Welcome <?=htmlspecialchars($_SESSION['user'])?></h2>
<p>This is a minimal demo dashboard.</p>
<p><a href="/logout.php">Logout</a></p>
</body>
</html>
