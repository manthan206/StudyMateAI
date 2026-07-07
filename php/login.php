<?php
session_start();
require 'config.php';

// If already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

if (isset($_GET['registered'])) {
    $message = "<p class='success'>Registration successful. Please login.</p>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "<p class='error'>Please enter your email and password.</p>";
    } else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() == 1) {

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                header("Location: dashboard.php");
                exit;

            } else {
                $message = "<p class='error'>Invalid password.</p>";
            }

        } else {
            $message = "<p class='error'>Email not found.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>StudyMateAI Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">

<div class="card">

<h1>Login</h1>

<?php echo $message; ?>

<form method="POST">

<input
type="email"
name="email"
placeholder="Email"
required>

<input
type="password"
name="password"
placeholder="Password"
required>

<button type="submit">
Login
</button>

</form>

<p>
Don't have an account?
<a href="register.php">Register</a>
</p>

</div>

</div>

</body>
</html>