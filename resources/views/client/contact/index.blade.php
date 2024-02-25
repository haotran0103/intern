@extends('client.layout')
@section('content')
<div class="contact-container">
    <div class="contact-details">
        <h2>Chi tiết liên hệ</h2>
        <p><strong>Email:</strong> <span id="email"></span></p>
        <p><strong>Địa chỉ:</strong> <span id="address"></span></p>
        <p><strong>Số điện thoại:</strong> <span id="phone"></span></p>
    </div>
    <div id="map"></div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- <script>
    $(document).ready(function() {
        // Gọi API readSetting
        $.ajax({
            url: 'api/v1/ReadSetting', // Thay đổi url này thành endpoint thực tế của API readSetting
            type: 'GET',
            success: function(response) {

                 response.forEach(element => {
                if(element.config_key === 'Địa chỉ') {
                    $('#address').text(element.config_value);
                } else if(element.config_key === 'Số điện thoại') {
                    $('#phone').text(element.config_value);
                } else if(element.config_key === 'Email') {
                    $('#email').text(element.config_value);
                }
            });
            },
            error: function(xhr, status, error) {
                // Xử lý khi có lỗi trong quá trình gọi API
                console.error('Lỗi khi gọi API readSetting:', error);
            }
        });
    });
    </script> --}}
    