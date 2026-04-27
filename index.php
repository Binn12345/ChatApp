<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chat App</title>

   
    <meta name="description" content="A simple real-time chat application where users can send messages instantly, share images, and communicate seamlessly.">

    <meta name="keywords" content="chat app, real-time chat, messaging system, PHP chat, JavaScript chat app">
    <meta name="author" content="akositep">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial;
        }

        body {
            height: 100vh;
            background: #f0f2f5;
        }

        /* LAYOUT */
        .container {
            display: flex;
            height: 100vh;
        }

        /* SIDEBAR */
        .users {
            width: 25%;
            background: #fff;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }

        .users ul {
            list-style: none;
        }

        .users li {
            padding: 12px;
            cursor: pointer;
        }

        .users li:hover {
            background: #f5f5f5;
        }

        /* TOP BAR */
        .top-bar {
            display: flex;
            justify-content: space-between;
            padding: 12px;
            background: #fff;
            border-bottom: 1px solid #ddd;
        }

        .user-name {
            text-decoration: none;
            font-weight: bold;
            color: #333;
        }

        /* CHAT */
        .chat {
            width: 75%;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 15px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        /* MESSAGES */
        #messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message {
            padding: 10px 14px;
            margin: 6px;
            border-radius: 20px;
            max-width: 70%;
        }

        .sent {
            background: #1877f2;
            color: #fff;
            align-self: flex-end;
        }

        .received {
            background: #e4e6eb;
        }

        /* INPUT */
        .chat-input {
            display: flex;
            gap: 8px;
            padding: 10px;
            background: #fff;
            border-top: 1px solid #ddd;
        }

        .chat-input input[type=text] {
            flex: 1;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
        }

        .chat-input label {
            background: #e4e6eb;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
        }

        .chat-input input[type=file] {
            display: none;
        }

        .chat-input button {
            background: #1877f2;
            color: #fff;
            border: none;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
        }

        /* LOGOUT */
        .logout-btn {
            background: #e41e3f;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
        }

        /* DARK MODE */
        body.dark {
            background: #18191a;
            color: #e4e6eb;
        }

        body.dark .users,
        body.dark .chat-header,
        body.dark .chat-input,
        body.dark .top-bar {
            background: #242526;
        }

        body.dark .received {
            background: #3a3b3c;
            color: #fff;
        }

        /* MOBILE */
        @media (max-width: 768px) {

            .container {
                flex-direction: column;
            }

            .users {
                width: 100%;
                height: 60px;
                overflow: hidden;
            }

            .users.open {
                height: 50vh;
            }

            .users ul {
                display: none;
            }

            .users.open ul {
                display: block;
            }

            .chat {
                width: 100%;
                height: calc(100vh - 60px);
            }

            .message {
                max-width: 85%;
            }
        }
    </style>

</head>

<body>

    <div class="container">

        <!-- SIDEBAR -->
        <div class="users">
            <div class="top-bar" onclick="toggleUsers()">

                <a href="profile.php?id=<?php echo $user_id; ?>" class="user-name">
                    <?php echo htmlspecialchars($user['name']); ?>
                </a>

                <div>
                    <button onclick="toggleDarkMode()">🌙</button>
                    <button onclick="confirmLogout()" class="logout-btn">Logout</button>
                </div>
            </div>

            <ul id="userList"></ul>
        </div>

        <!-- CHAT -->
        <div class="chat">

            <div class="chat-header" id="chatHeader">
                Select a user
            </div>

            <div id="messages"></div>

            <form id="chatForm" class="chat-input">
                <input type="hidden" id="receiver_id">

                <input type="text" id="message" placeholder="Type message...">

                <label for="image">📎</label>
                <input type="file" id="image" multiple accept="image/*">

                <span id="fileCount"></span>

                <button>➤</button>
            </form>

        </div>

    </div>

    <script>
        let receiver_id = null;

        // SELECT USER
        function selectUser(id, name) {
            receiver_id = id;
            document.getElementById("chatHeader").innerText = name;
            loadMessages();
        }

        // LOAD USERS
        function loadUsers() {
            fetch("fetch_users.php")
                .then(res => res.text())
                .then(data => {
                    document.getElementById("userList").innerHTML = data;

                    document.querySelectorAll("#userList li").forEach(li => {
                        li.onclick = () => selectUser(li.dataset.id, li.dataset.name);
                    });
                });
        }

        // LOAD MESSAGES
        function loadMessages() {
            if (!receiver_id) return;

            fetch("fetch_messages.php?receiver_id=" + receiver_id)
                .then(res => res.text())
                .then(data => {
                    let box = document.getElementById("messages");
                    box.innerHTML = data;
                    box.scrollTop = box.scrollHeight;
                });
        }

        // SEND MESSAGE
        document.getElementById("chatForm").addEventListener("submit", function(e) {
            e.preventDefault();

            if (!receiver_id) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select a user first'
                });
                return;
            }

            let formData = new FormData();
            formData.append("receiver_id", receiver_id);
            formData.append("message", document.getElementById("message").value);

            let img = document.getElementById("image");

            if (img.files.length > 0) {
                formData.append("image", img.files[0]);
            }

            fetch("send_message.php", {
                method: "POST",
                body: formData
            }).then(() => {
                document.getElementById("message").value = "";
                img.value = "";
                loadMessages();
            });
        });

        // DARK MODE
        function toggleDarkMode() {
            document.body.classList.toggle("dark");
            localStorage.setItem("darkMode", document.body.classList.contains("dark"));
        }

        if (localStorage.getItem("darkMode") === "true") {
            document.body.classList.add("dark");
        }

        // MOBILE SIDEBAR
        function toggleUsers() {
            if (window.innerWidth <= 768) {
                document.querySelector(".users").classList.toggle("open");
            }
        }

        // LOGOUT
        function confirmLogout() {
            Swal.fire({
                title: 'Logout?',
                text: 'You will be signed out',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1877f2'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "logout.php";
                }
            });
        }

        // AUTO REFRESH
        setInterval(loadMessages, 1000);

        // INIT
        loadUsers();
    </script>

</body>

</html>