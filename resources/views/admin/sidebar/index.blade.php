<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark  style="background-color: #2e8ff7;" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Bảng điều khiển</div>
                <a class="nav-link" href="/dashboard">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Trang chủ
                </a>
                <div class="sb-sidenav-menu-heading">Bài viết</div>
                <a class="nav-link" href="{{ url('/posts') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Quản lí bài viết
                </a>

                <a class="nav-link" href="{{ url('/category') }}" data-bs-target="#collapsePages" aria-expanded="false"
                    aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    Quản lí thể loại
                </a>
                {{-- <div class="sb-sidenav-menu-heading">Tài khoản</div>
                
                <a class="nav-link collapsed admin-use" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="bi bi-person-gear"></i></div>
                    Quản lí quyền
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                        <a class="nav-link collapsed" href="{{URL::asset('/account')}}" >
                            Quản lí Tài khoản
                        </a>

                        <a class="nav-link collapsed" href="{{URL::asset('/history')}}" >
                            Lịch sử hoạt động
                        </a>
                    </nav>
                </div> --}}
                <div class="sb-sidenav-menu-heading">Trang web</div>
                <a class="nav-link" href="{{ url('/settings') }}">
                    <div class="sb-nav-link-icon"><i class="bi bi-gear"></i></div>
                    Quản lí trang
                </a>


            </div>

        </div>
    </nav>
</div>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var userRole = sessionStorage.getItem('role');
        if (userRole === 'root') {
            var adminUseHTML = `
            <div class="sb-sidenav-menu-heading">Tài khoản</div>
                
                <a class="nav-link collapsed admin-" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="bi bi-person-gear"></i></div>
                    Quản lí quyền
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                        <a class="nav-link collapsed" href="{{ URL::asset('/account') }}" >
                            Quản lí Tài khoản
                        </a>

                        <a class="nav-link" href="{{ url('/trash') }}">
                    <div class="sb-nav-link-icon"><i class="bi bi-trash"></i></div>
                    Thùng rác
                </a>
                    </nav>
                </div>
            `;
            $('.sb-sidenav-menu .nav').append(adminUseHTML);
        }
    });
</script>
