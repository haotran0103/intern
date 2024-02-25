<nav class="sb-topnav navbar navbar-expand navbar-dark background-color: #2e8ff7;" style="min-height: 80px;">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="{{URL::asset('/dashboard')}}">
        <img src="{{asset('icon/logo.png')}}" alt="" class="w-auto mt-1" style="max-width: 70px;">
    </a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">
            <a class="message-icon" href="{{URL::asset('/chat')}}"><i style="font-size: 22px" class="me-lg-4 bi bi-chat-left-dots"></i></a>
        </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i style="font-size: 22px" class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="#!">Settings</a></li>
                <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a id="logoutButton" class="dropdown-item" href="#!">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>
<style>
    .message-icon {
    color: rgb(230, 231, 233); /* Màu icon message */
}

.message-icon:hover {
    color: #fff; /* Màu icon message khi hover */
}

</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bắt sự kiện click vào nút "Logout"
        document.getElementById('logoutButton').addEventListener('click', function(event) {
            event.preventDefault();

            // Gọi API logout
            fetch('/api/v1/auth/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${sessionStorage.getItem('accessToken')}`
                }
            })
            .then(response => {
                if (response.ok) {
                    // Đăng xuất thành công, chuyển hướng hoặc thực hiện các thao tác khác
                    window.location.href = '/login';
                } else {
                    // Xử lý lỗi nếu cần
                    console.error('Logout failed:', response);
                }
            })
            .catch(error => {
                // Xử lý lỗi nếu cần
                console.error('Logout error:', error);
            });
        });
    });
</script>
