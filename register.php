<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>

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

.container {
    background: #fff;
    padding: 30px;
    width: 370px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from {opacity:0; transform: translateY(20px);}
    to {opacity:1; transform: translateY(0);}
}

.container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #1877f2;
}

.input-group {
    margin-bottom: 15px;
    position: relative;
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

/* show password */
.toggle-pass {
    position: absolute;
    right: 10px;
    top: 12px;
    cursor: pointer;
    font-size: 12px;
    color: #1877f2;
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

.success {
    color: green;
    text-align: center;
    margin-bottom: 10px;
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

<div class="container">
    <h2>Create Account</h2>

    <?php if(isset($success)) echo "<div class='success'>$success</div>"; ?>
    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <div class="input-group">
            <input type="text" name="name" placeholder="Full Name" required>
        </div>

        <div class="input-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-group">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span class="toggle-pass" onclick="togglePassword()">Show</span>
        </div>

        <button name="register">Register</button>
    </form>

    <div class="footer">
        have an a account? <a href="login.php">Login</a>
    </div>
</div>

<script>
function togglePassword(){
    let pass = document.getElementById("password");
    pass.type = (pass.type === "password") ? "text" : "password";
}
</script>

</body>
</html>

<?php
if(isset($_POST['register'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $passwordRaw = $_POST['password'];

    // Basic validation
    if(strlen($passwordRaw) < 6){
        echo "<script>alert('Password must be at least 6 characters');</script>";
        exit;
    }

    // Check if email exists
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if($check->num_rows > 0){
        echo "<script>alert('Email already exists');</script>";
        exit;
    }

    $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

    $conn->query("INSERT INTO users (name,email,password) 
                  VALUES ('$name','$email','$password')");

    echo "<script>alert('Registered successfully'); window.location='login.php';</script>";
}
?>