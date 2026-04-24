<?php
include 'config.php';
// session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// GET USER DATA
$stmt = $conn->prepare("SELECT name, email, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// UPDATE PROFILE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $profile_pic = $user['profile_pic'];

    // HANDLE IMAGE UPLOAD
    if (!empty($_FILES['profile_pic']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['profile_pic']['tmp_name']);
        $profile_pic = $imageData;
    }


    // var_dump('<pre>',$_FILES,$profile_pic);
    // IF PASSWORD CHANGED
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users 
            SET name=?, email=?, password=?, profile_pic=? 
            WHERE id=?
        ");
        $null = $profile_pic;
        $stmt->bind_param("ssssi", $username, $email, $hashed_password, $null, $user_id);

        if ($profile_pic) {
            $stmt->send_long_data(3, $profile_pic);
        }
    } else {
        $stmt = $conn->prepare("
            UPDATE users 
            SET name=?, email=?, profile_pic=? 
            WHERE id=?
        ");
        $null = $profile_pic;
        $stmt->bind_param("sssi", $username, $email, $null, $user_id);

        if ($profile_pic) {
            $stmt->send_long_data(2, $profile_pic);
        }
    }

    $stmt->execute();

    echo "<script>alert('Profile updated successfully');</script>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Profile Settings</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f0f2f5;
        }

        /* TOP BAR */
        .topbar {
            background: #1877f2;
            color: white;
            padding: 15px 20px;
            font-size: 20px;
            font-weight: bold;
        }

        /* CENTER CARD */
        .container {
            width: 420px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* PROFILE HEADER */
        .profile-header {
            text-align: center;
            padding: 25px 20px;
            border-bottom: 1px solid #ddd;
        }

        .profile-header img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #1877f2;
        }

        .profile-header h2 {
            margin: 10px 0 0;
            font-size: 22px;
        }

        /* FORM */
        .form-box {
            padding: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #1877f2;
            outline: none;
        }

        .file-input {
            padding: 8px;
            background: #f5f6f7;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #1877f2;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #166fe5;
        }

        /* 📱 MOBILE RESPONSIVE */
        @media (max-width: 600px) {

            .container {
                width: 95%;
                margin: 20px auto;
                border-radius: 8px;
            }

            .topbar {
                font-size: 18px;
                text-align: center;
            }

            .profile-header img {
                width: 90px;
                height: 90px;
            }

            .profile-header h2 {
                font-size: 18px;
            }

            input {
                font-size: 14px;
                padding: 10px;
            }

            button {
                font-size: 14px;
                padding: 10px;
            }
        }

        /* SMALL PHONE (extra polish) */
        @media (max-width: 400px) {
            .profile-header {
                padding: 20px 10px;
            }

            .form-box {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="topbar">
        <a href="../ChatApp" style="color:white; text-decoration:none;">
            ChatApp 
        </a>
    </div>

    <div class="container">

        <!-- PROFILE HEADER -->
        <div class="profile-header">

            <?php if (!empty($user['profile_pic'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($user['profile_pic']) ?>">
            <?php else: ?>
                <img src="default.png">
            <?php endif; ?>

            <h2><?= htmlspecialchars($user['name']) ?></h2>
        </div>

        <!-- FORM -->
        <div class="form-box">

            <form method="POST" enctype="multipart/form-data">

                <input type="text" name="username" value="<?= htmlspecialchars($user['name']) ?>" required>

                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <input type="password" name="password" placeholder="New Password (optional)">

                <input class="file-input" type="file" name="profile_pic" accept="image/*">

                <button type="submit">Update Profile</button>

            </form>

        </div>

    </div>

</body>

</html>