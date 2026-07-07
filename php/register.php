<?php
session_start();
require 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $message = "Please fill all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } else {

        // Check email
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {

            $message = "Email already registered.";

        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");

            if ($insert->execute([$name,$email,$hash])) {

                header("Location: login.php?registered=1");
                exit;

            } else {

                $message = "Registration Failed.";

            }

        }

    }

}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>StudyMateAI Register</title>

<link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="container">

<div class="card">

<h1>Create Account</h1>

<?php
if($message!=""){
echo "<p class='error'>$message</p>";
}
?>

<form method="POST">

<input
type="text"
name="name"
placeholder="Full Name"
required>

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

<input
type="password"
name="confirm_password"
placeholder="Confirm Password"
required>

<button type="submit">
Register
</button>

</form>

<p>

Already have an account?

<a href="login.php">

Login

</a>

</p>

</div>

</div>

</body>

</html>