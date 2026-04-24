<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #1877f2, #42a5f5);
}

.login-container {
    background: #fff;
    padding: 30px;
    width: 350px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}

.login-container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #1877f2;
}

.input-group {
    margin-bottom: 15px;
}

.input-group input {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ddd;
    outline: none;
    transition: 0.3s;
}

.input-group input:focus {
    border-color: #1877f2;
    box-shadow: 0 0 5px rgba(24,119,242,0.3);
}

button {
    width: 100%;
    padding: 12px;
    background: #1877f2;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #0f5ec7;
}

.error {
    color: red;
    text-align: center;
    margin-bottom: 10px;
}

.footer {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
}

.footer a {
    color: #1877f2;
    text-decoration: none;
}
</style>

</head>
<body>

<div class="login-container">
    <h2>Chat Login</h2>

    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <div class="input-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button name="login">Login</button>
    </form>

    <div class="footer">
        No account? <a href="register.php">Register</a>
    </div>
</div>

</body>
</html>

<?php
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    if($user && password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        echo "<script>alert('Invalid login'); window.location='login.php';</script>";
    }
}
?>