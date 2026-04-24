// let receiver_id = null;

// function selectUser(id){
//     receiver_id = id;
//     document.getElementById("receiver_id").value = id;
//     loadMessages();
// }

// function loadMessages(){
//     if(!receiver_id) return;

//     fetch(`fetch_messages.php?receiver_id=${receiver_id}`)
//     .then(res => res.text())
//     .then(data => {
//         document.getElementById("messages").innerHTML = data;
//     });
// }

// document.getElementById("chatForm").addEventListener("submit", function(e) {
//     e.preventDefault();

//     if (!receiver_id) return alert("Select a user first");

//     const messageInput = document.getElementById("message");
//     const imageInput = document.getElementById("image");

//     let formData = new FormData();
//     formData.append("receiver_id", receiver_id);
//     formData.append("message", messageInput.value);

//     if (imageInput && imageInput.files && imageInput.files.length > 0) {
//         formData.append("image", imageInput.files[0]);
//     }

//     fetch("send_message.php", {
//         method: "POST",
//         body: formData
//     })
//     .then(res => res.text())
//     .then(() => {
//         messageInput.value = "";
//         imageInput.value = "";
//         loadMessages();
//     });
// });

// setInterval(loadMessages, 1000);

// fetch("fetch_users.php")
// .then(res=>res.text())
// .then(data=>{
//     document.getElementById("userList").innerHTML = data;
// });