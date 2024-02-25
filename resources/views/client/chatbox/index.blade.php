<meta name="csrf-token" content="{{ csrf_token() }}">

<button id="openChatBtn">Open Chat</button>
<div id="chatBox" class="chat-box">
    <div class="chat-header">
        <h2>Chat Box</h2>
        <button id="closeChatBtn">Close</button>
    </div>
    <div class="chat-content" id="chatContent">
        <!-- Tin nhắn sẽ được thêm vào đây -->
    </div>
    <div class="chat-footer">
        <input type="file" id="imageInput" accept="image/*">
        <input type="text" id="messageInput" placeholder="Type your message...">
        <button id="sendMessageBtn">Send</button>
    </div>
</div>

<link rel="stylesheet" href="{{ URL::asset('assets/css/chatbox.css') }}">
<script src="{{ URL::asset('assets/js/bootstrap.js') }}" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.3/laravel-echo.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.3.2/socket.io.min.js"></script>
<script src="https://cdn.socket.io/socket.io-1.3.4.js"></script>
<script>
    var token = localStorage.getItem('chatToken');
    window.Echo.chanel(`chat.${token}`)
    .listen('chatEvent', (e) => {
        console.log(e);
    });
</script>
<script>
    $(document).ready(function() {
        // Hàm hiển thị tin nhắn từ người nhận
        function displayReceiverMessage(content, timestamp) {
            var chatContent = document.getElementById('chatContent');
            var messageElement = document.createElement('div');
            messageElement.classList.add('chat-message', 'receiver');
            messageElement.innerHTML = `
        <div class="message-content">${content} <br> <div style="color: white" class="message-details">${timestamp}</div></div>
    `;
            chatContent.appendChild(messageElement);
            chatContent.scrollTop = chatContent.scrollHeight;
        }
    })
</script>
<script>
    document.getElementById('openChatBtn').addEventListener('click', function() {
        document.getElementById('openChatBtn').style.display = 'none';
        document.getElementById('chatBox').style.display = 'block';
    });

    document.getElementById('closeChatBtn').addEventListener('click', function() {
        document.getElementById('openChatBtn').style.display = 'block';
        document.getElementById('chatBox').style.display = 'none';
    });

    document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
    document.getElementById('messageInput').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    });

    function sendMessage() {
        var messageInput = document.getElementById('messageInput');
        var message = messageInput.value.trim();
        var token = localStorage.getItem("chatToken");

        if (!token) {
            token = generateToken();
            localStorage.setItem("chatToken", token);
        }

        if (message !== '') {
            // Tạo FormData để gửi dữ liệu lên server
            var formData = new FormData();
            formData.append('content', message);
            formData.append('guest_token', token);

            // Gửi tin nhắn lên server thông qua API
            fetch(`api/v1/send-message-to-admin`, {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    // Sau khi gửi tin nhắn thành công, hiển thị tin nhắn lên giao diện
                    if (data.success) {
                        // displaySenderMessage(message, getCurrentTime());
                        // messageInput.value = '';
                        // messageInput.focus();
                    } else {
                        console.error('Error sending message:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                });
        }
    }

    function generateToken() {
        var token = Math.floor(10000000000 + Math.random() *
            90000000000); // Tạo số ngẫu nhiên từ 10000000000 đến 99999999999
        return token.toString(); // Chuyển số thành chuỗi và trả về
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Kiểm tra xem token đã tồn tại trong Local Storage chưa
        token = localStorage.getItem("chatToken");

        // Nếu token đã tồn tại, gọi API để lấy tin nhắn
        if (token) {
            fetch(`api/v1/guest-get-message/${token}`)
                .then(response => response.json())
                .then(data => {
                    // Xử lý dữ liệu nhận được từ API
                    if (data.messages) {
                        // Hiển thị tin nhắn lên giao diện
                        data.messages.forEach(message => {
                            if (message.sender_type === 'customer') {
                                displaySenderMessage(message.content, message.timestamp);
                            } else {
                                displayReceiverMessage(message.content, message.timestamp);
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching messages:', error);
                });
        }
    });

    // Hàm hiển thị tin nhắn từ người gửi
    function displaySenderMessage(content, timestamp) {
        var chatContent = document.getElementById('chatContent');
        var messageElement = document.createElement('div');
        messageElement.classList.add('chat-message', 'sender');
        messageElement.innerHTML = `
        <div class="message-content">${content} <br> <div style="color: white" class="message-details">${timestamp}</div></div>
    `;
        chatContent.appendChild(messageElement);
        chatContent.scrollTop = chatContent.scrollHeight;
    }

    // Hàm hiển thị tin nhắn từ người nhận
    function displayReceiverMessage(content, timestamp) {
        var chatContent = document.getElementById('chatContent');
        var messageElement = document.createElement('div');
        messageElement.classList.add('chat-message', 'receiver');
        messageElement.innerHTML = `
        <div class="message-content">${content} <br> <div style="color: white" class="message-details">${timestamp}</div></div>
    `;
        chatContent.appendChild(messageElement);
        chatContent.scrollTop = chatContent.scrollHeight;
    }

    function getCurrentTime() {
        var now = new Date();
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        var date = now.getDate().toString().padStart(2, '0');
        var month = (now.getMonth() + 1).toString().padStart(2, '0');
        return `${hours}:${minutes} - ${date}/${month}`;
    }
</script>
