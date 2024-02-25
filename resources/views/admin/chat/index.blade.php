@extends('admin.layout')
@section('content')
    <div class="container">
        <h3 class=" text-center">Messaging</h3>
        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <div class="headind_srch">
                        <div class="recent_heading">
                            <h4>Recent</h4>
                        </div>
                    </div>
                    <div class="inbox_chat">

                    </div>
                </div>
                <div class="mesgs">
                    <div class="msg_history">


                    </div>
                    <div class="type_msg">
                        <div class="input_msg_write">
                            <input id="messageInput" type="text" class="write_msg" placeholder="Type a message" />
                            <button class="msg_send_btn" id="sendMessageBtn" type="button"><i class="fa fa-paper-plane-o"
                                    aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Include Echo library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.3/laravel-echo.min.js"></script>


        <script>
            $(document).ready(function() {
                // Khai báo biến global để lưu trữ token của người dùng được click
                var currentCustomerToken = '';

                makeRequest('/api/v1/admin-get-message', {
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                        }
                    })
                    .then(function(data) {
                        if (data && data.customer_data && data.customer_data.length > 0) {
                            data.customer_data.forEach(function(message) {
                                var html = '';
                                html += '<div class="chat_list" data-customer-token="' + message
                                    .customer_token + '">';
                                html += '<div class="chat_people">';
                                html +=
                                    '<div class="chat_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>';
                                html += '<div class="chat_ib">';
                                html += '<h5>' + message.customer_token + '<span class="chat_date">' +
                                    formatDate(message.latest_message.created_at) + '</span>';
                                // Thêm số lượng tin nhắn chưa đọc (nếu có)
                                if (message.unread_messages_count > 0) {
                                    var unreadCountHtml = '<span class="unread-count';
                                    if (message.unread_messages_count > 9) {
                                        unreadCountHtml += ' over-nine';
                                    }
                                    unreadCountHtml += '">' + (message.unread_messages_count > 9 ? '9+' :
                                        message.unread_messages_count) + '</span>';
                                    html += unreadCountHtml;
                                }
                                html += '</h5>';
                                html += '<p>' + message.latest_message.content + '</p>';
                                html += '</div></div></div>';
                                $('.inbox_chat').append(html);
                            });
                        }

                        // Đặt mã xử lý sự kiện click vào phần tử .chat_list sau khi thêm dữ liệu vào DOM
                        $('.chat_list').click(function() {
                            currentCustomerToken = $(this).data(
                                'customer-token'); // Lưu trữ token của người dùng được click
                            var customerToken = currentCustomerToken;
                            // Kiểm tra nếu đang ở màn hình điện thoại di động
                            if ($(window).width() < 600) {
                                // Cuộn xuống phần tử .msg_history để hiển thị hộp thoại chi tiết tin nhắn
                                $('html, body').animate({
                                    scrollTop: $(".msg_history").offset().top
                                }, 100);
                                getCustomerMessages(customerToken);
                            } else {
                                console.log('a');
                                getCustomerMessages(customerToken);
                            }
                            listenToChannel(customerToken);
                        });
                    })
                    .catch(function(error) {
                        console.error(error);
                    });

                // Hàm định dạng lại ngày tạo (created_at)
                function formatDate(dateString) {
                    var date = new Date(dateString);
                    var month = date.getMonth() + 1;
                    var day = date.getDate();
                    var year = date.getFullYear();
                    return day + '/' + month + '/' + year;
                }
                window.Echo = new Echo({
                    broadcaster: 'socket.io',
                    host: window.location.hostname + ':6001', // Adjust port if necessary
                });

                function listenToChannel(token) {
                    const channelName = 'laravel_database_chat.' + token;
                    window.Echo.channel(channelName)
                        .listen('chatEvent', (data) => {
                            console.log('Received event:', data);
                            // Handle the received event data
                        });
                }

                function getCustomerMessages(customerToken) {
                    $('.msg_history').empty();
                    makeRequest('/api/v1/guest-get-message/' + customerToken, {
                            method: 'GET',
                            headers: {
                                'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                            }
                        })
                        .then(function(data) {
                            data.data.forEach(function(message) {
                                if (message.sender_type === "admin") {
                                    var outgoingMsg = $(
                                        '<div class="outgoing_msg"><div class="sent_msg"><p>' + message
                                        .content + '</p><span class="time_date">' + formatDate(
                                            message.created_at) + '</span></div></div></div>');
                                    $('.msg_history').append(outgoingMsg);
                                } else if (message.sender_type === "customer") {
                                    var incomingMsg = $(
                                        '<div class="incoming_msg"><div class="incoming_msg_img"><img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"></div><div class="received_msg"><div class="received_withd_msg"><p>' +
                                        message.content + '</p><span class="time_date">' +
                                        formatDate(
                                            message.created_at) + '</span></div></div></div>');
                                    $('.msg_history').append(incomingMsg);
                                }

                            });

                        })
                        .catch(function(error) {
                            console.error(error);
                        });
                }

                document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
                document.getElementById('messageInput').addEventListener('keypress', function(event) {
                    if (event.key === 'Enter') {
                        sendMessage();
                    }
                });

                // Hàm gửi tin nhắn
                function sendMessage() {
                    var messageInput = document.getElementById('messageInput');
                    var message = messageInput.value.trim();
                    // Sử dụng token của người dùng được click trước đó
                    var token = currentCustomerToken;

                    if (message !== '') {
                        // Tạo FormData để gửi dữ liệu lên server
                        var formData = new FormData();
                        formData.append('content', message);
                        formData.append('guest_token', token);

                        // Gửi tin nhắn lên server thông qua API
                        fetch(`api/v1/reply-message-to-guest`, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                                }
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
            });
        </script>

        <style>
            .unread-count {
                background-color: red;
                color: white;
                border-radius: 50%;
                padding: 2px 6px;
                font-size: 12px;
                margin-right: 10px;
            }

            .unread-count.over-nine {
                padding: 2px 4px;
            }

            .container {
                max-width: 1170px;
                margin: auto;
            }

            img {
                max-width: 100%;
            }

            .inbox_people {
                background: #f8f8f8 none repeat scroll 0 0;
                float: left;
                overflow: hidden;
                width: 40%;
                border-right: 1px solid #c4c4c4;
            }

            .inbox_msg {
                border: 1px solid #c4c4c4;
                clear: both;
                overflow: hidden;
            }

            .top_spac {
                margin: 20px 0 0;
            }


            .recent_heading {
                float: left;
                width: 40%;
            }

            .srch_bar {
                display: inline-block;
                text-align: right;
                width: 60%;
            }

            .headind_srch {
                padding: 10px 29px 10px 20px;
                overflow: hidden;
                border-bottom: 1px solid #c4c4c4;
            }

            .recent_heading h4 {
                color: #05728f;
                font-size: 21px;
                margin: auto;
            }

            .srch_bar input {
                border: 1px solid #cdcdcd;
                border-width: 0 0 1px 0;
                width: 80%;
                padding: 2px 0 4px 6px;
                background: none;
            }

            .srch_bar .input-group-addon button {
                background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
                border: medium none;
                padding: 0;
                color: #707070;
                font-size: 18px;
            }

            .srch_bar .input-group-addon {
                margin: 0 0 0 -27px;
            }

            .chat_ib h5 {
                font-size: 15px;
                color: #464646;
                margin: 0 0 8px 0;
            }

            .chat_ib h5 span {
                font-size: 13px;
                float: right;
            }

            .chat_ib p {
                font-size: 14px;
                color: #989898;
                margin: auto
            }

            .chat_img {
                float: left;
                width: 11%;
            }

            .chat_ib {
                float: left;
                padding: 0 0 0 15px;
                width: 88%;
            }

            .chat_people {
                overflow: hidden;
                clear: both;
            }

            .chat_list {
                border-bottom: 1px solid #c4c4c4;
                margin: 0;
                padding: 18px 16px 10px;
            }

            .inbox_chat {
                height: 550px;
                overflow-y: scroll;
            }

            .active_chat {
                background: #ebebeb;
            }

            .incoming_msg_img {
                display: inline-block;
                width: 6%;
            }

            .received_msg {
                display: inline-block;
                padding: 0 0 0 10px;
                vertical-align: top;
                width: 92%;
            }

            .received_withd_msg p {
                background: #ebebeb none repeat scroll 0 0;
                border-radius: 3px;
                color: #646464;
                font-size: 14px;
                margin: 0;
                padding: 5px 10px 5px 12px;
                width: 100%;
            }

            .time_date {
                color: #747474;
                display: block;
                font-size: 12px;
                margin: 8px 0 0;
            }

            .received_withd_msg {
                width: 57%;
            }

            .mesgs {
                float: left;
                padding: 30px 15px 0 25px;
                width: 60%;
            }

            .sent_msg p {
                background: #05728f none repeat scroll 0 0;
                border-radius: 3px;
                font-size: 14px;
                margin: 0;
                color: #fff;
                padding: 5px 10px 5px 12px;
                width: 100%;
            }

            .outgoing_msg {
                overflow: hidden;
                margin: 26px 0 26px;
            }

            .sent_msg {
                float: right;
                width: 46%;
            }

            .input_msg_write input {
                background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
                border: medium none;
                color: #4c4c4c;
                font-size: 15px;
                min-height: 48px;
                width: 100%;
            }

            .type_msg {
                border-top: 1px solid #c4c4c4;
                position: relative;
            }

            .msg_send_btn {
                background: #05728f none repeat scroll 0 0;
                border: medium none;
                border-radius: 50%;
                color: #fff;
                cursor: pointer;
                font-size: 17px;
                height: 33px;
                position: absolute;
                right: 0;
                top: 11px;
                width: 33px;
            }

            .messaging {
                padding: 0 0 50px 0;
            }

            .msg_history {
                height: 516px;
                overflow-y: auto;
            }

            /* Thêm media query để điều chỉnh giao diện trên điện thoại */
            /* Thêm các điều chỉnh CSS cho giao diện trên điện thoại di động */
            @media only screen and (max-width: 600px) {

                /* Điều chỉnh kích thước và căn chỉnh cho các phần tử chính */
                .inbox_people {
                    width: 100%;
                    border-right: none;
                }

                .mesgs {
                    width: 100%;
                    padding: 15px;
                }

                .chat_img {
                    width: 20%;
                }

                .chat_ib {
                    width: 80%;
                }

                /* Xử lý kích thước hình ảnh */
                img {
                    max-width: 50px;
                    /* Điều chỉnh kích thước hình ảnh cho phù hợp với màn hình nhỏ */
                }
            }
        </style>
