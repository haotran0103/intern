<footer class="mt-auto">
    <div class="container-fluid" style="background-color: #2e8ff7;">
        <div class="row">
            <div class="col-lg-4">
                <div class="footer-text d-flex align-items-center">
                    <img src="{{asset('icon/logo.png')}}" alt="" class="logo-footer">
                    <div class="text-left custom-text-size-footer text-white font-weight-bold">
                        <span>Cục thuế thành phố Hồ Chí Minh</span><br>
                        <span>chi cục thuế quận 8</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="footer-text d-flex align-items-center">
                    <div class="text-left custom-text-size-footer text-white font-weight-bold">
                        <span>Trang thông tin Chi Cục Thuế Quận 8</span><br>
                        <p>Địa chỉ: <span id="address"></span></p>
                        <p>Điện thoại: <span id="phone"></span></p>
                        <p>Fax: <span id="fax"></span></p>
                        <p>Thời gian làm việc: <span id="workingtime"></span></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="footer-text d-flex align-items-center">
                    <div class="text-left custom-text-size-footer text-white font-weight-bold">
                        <span>Kết nối Chi Cục Thuế Quận 8</span><br>
                        <p>© Bản quyền 2024 - Chi Cục Thuế Quận 8</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>
<style>
    .footer-text {
        padding: 20px;
    }
    .footer-text p {
    margin: 0;
    padding: 0;
}


    .custom-text-size-footer span{
        font-size: 15px;
        font-weight: bold;
    }

    .logo-footer {
        width: 80px;
        height: 80px;
    }

</style>
<script>
    $(document).ready(function() {
        // Gọi API readSetting
        $.ajax({
            url: 'api/v1/ReadSetting', // Thay đổi url này thành endpoint thực tế của API readSetting
            type: 'GET',
            success: function(response) {

                 response.forEach(element => {
                if(element.config_key === 'address') {
                    $('#address').text(element.config_value);
                } else if(element.config_key === 'phone') {
                    $('#phone').text(element.config_value);
                } else if(element.config_key === 'Fax') {
                    $('#fax').text(element.config_value);
                }else if(element.config_key === 'workingtime') {
                    $('#workingtime').text(element.config_value);
                }
                
            });
            },
            error: function(xhr, status, error) {
                // Xử lý khi có lỗi trong quá trình gọi API
                console.error('Lỗi khi gọi API readSetting:', error);
            }
        });
    });
    </script>
    