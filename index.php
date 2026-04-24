<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Kunin current user info
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Chat App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            height: 100vh;
            background: #f0f2f5;
        }

        /* Layout */
        .container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .users {
            width: 25%;
            background: #fff;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }

        .users h3 {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .users ul {
            list-style: none;
        }

        .users li {
            padding: 12px;
            cursor: pointer;
            transition: 0.2s;
        }

        .users li:hover {
            background: #f5f5f5;
        }

        /* Chat Area */
        .chat {
            width: 75%;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .chat-header {
            padding: 15px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        /* Messages */
        #messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message {
            padding: 10px 14px;
            margin: 5px;
            border-radius: 18px;
            max-width: 60%;
            font-size: 14px;
        }

        .sent {
            background: #1877f2;
            color: #fff;
            align-self: flex-end;
        }

        .received {
            background: #e4e6eb;
            align-self: flex-start;
        }

        /* Input */
        .chat-input {
            display: flex;
            padding: 10px;
            background: #fff;
            border-top: 1px solid #ddd;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            outline: none;
        }

        .chat-input button {
            margin-left: 10px;
            padding: 10px 15px;
            border: none;
            background: #1877f2;
            color: white;
            border-radius: 50%;
            cursor: pointer;
        }

        .chat-input button:hover {
            background: #0f5ec7;
        }

        /* Top bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #fff;
            border-bottom: 1px solid #ddd;
        }

        .logout {
            color: red;
            text-decoration: none;
            font-size: 14px;
        }

        .user-name {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .user-name:hover {
            color: #1877f2;
        }

        body.dark {
            background: #18191a;
            color: #e4e6eb;
        }

        /* containers */
        body.dark .users,
        body.dark .chat-header,
        body.dark .chat-input,
        body.dark .top-bar {
            background: #242526;
            border-color: #3a3b3c;
        }

        /* sidebar text */
        body.dark .users li {
            color: #e4e6eb;
        }

        body.dark .users li:hover {
            background: #3a3b3c;
        }

        /* messages */
        body.dark .received {
            background: #3a3b3c;
            color: #e4e6eb;
        }

        body.dark .sent {
            background: #1877f2;
        }

        /* inputs */
        body.dark input {
            background: #3a3b3c;
            color: #e4e6eb;
            border: 1px solid #555;
        }

        /* buttons */
        body.dark .chat-input button {
            background: #1877f2;
        }

        body.dark a {
            color: #e4e6eb;
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- Sidebar -->
        <div class="users">
            <div class="top-bar">
                <a href="profile.php?id=<?php echo $user_id; ?>" class="user-name">
                    <?php echo htmlspecialchars($user['name']); ?>
                </a>

                <div>
                    <button onclick="toggleDarkMode()" style="margin-right:10px; padding:5px 10px; border:none; border-radius:5px; cursor:pointer;">
                        🌙
                    </button>

                    <a href="logout.php" class="logout">Logout</a>
                </div>
            </div>

            <ul id="userList"></ul>
        </div>

        <!-- Chat -->
        <div class="chat">

            <div class="chat-header" id="chatHeader">
                Select a user
            </div>

            <div id="messages"></div>

            <form id="chatForm" class="chat-input" enctype="multipart/form-data">

                <input type="hidden" id="receiver_id">

                <input type="text" id="message" placeholder="Type a message...">

                <input type="file" id="image" accept="image/*">

                <button>➤</button>

            </form>

        </div>

    </div>

    <script>
        let receiver_id = null;

        // Select user
        function selectUser(id, name) {
            console.log("Selected user:", id, name); // DEBUG

            receiver_id = id;
            document.getElementById("receiver_id").value = id;
            document.getElementById("chatHeader").innerText = name;
            loadMessages();
        }
        // Load users
        function loadUsers() {
            fetch("fetch_users.php")
                .then(res => res.text())
                .then(data => {
                    document.getElementById("userList").innerHTML = data;

                    // attach click with name
                    document.querySelectorAll("#userList li").forEach(li => {
                        li.addEventListener("click", function() {
                            selectUser(this.dataset.id, this.dataset.name);
                        });
                    });
                });
        }

        // Load messages
        function loadMessages() {
            if (!receiver_id) return;

            fetch(`fetch_messages.php?receiver_id=${receiver_id}`)
                .then(res => res.text())
                .then(data => {
                    let msgBox = document.getElementById("messages");
                    msgBox.innerHTML = data;
                    msgBox.scrollTop = msgBox.scrollHeight;
                });
        }

        // Send message
        document.getElementById("chatForm").addEventListener("submit", function(e) {
            e.preventDefault();

            if (!receiver_id) return alert("Select a user first");

            let formData = new FormData();
            formData.append("receiver_id", receiver_id);
            formData.append("message", document.getElementById("message").value);

            let imageInput = document.getElementById("image");

            if (imageInput.files.length > 0) {
                formData.append("image", imageInput.files[0]);
            }

            fetch("send_message.php", {
                method: "POST",
                body: formData
            }).then(() => {
                document.getElementById("message").value = "";
                imageInput.value = "";
                loadMessages();
            });
        });

        // Auto refresh
        setInterval(loadMessages, 1000);

        // Init
        loadUsers();


        function toggleDarkMode() {
            document.body.classList.toggle("dark");

            // save preference
            if (document.body.classList.contains("dark")) {
                localStorage.setItem("darkMode", "on");
            } else {
                localStorage.setItem("darkMode", "off");
            }
        }

        // load saved mode
        window.onload = function() {
            loadUsers();

            if (localStorage.getItem("darkMode") === "on") {
                document.body.classList.add("dark");
            }
        };
    </script>

</body>

</html>