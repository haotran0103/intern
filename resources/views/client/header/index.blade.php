<header>
    <div class="container-fluid">
        <div class="row">
            <div class="bannerimage">
                <div class="header-text d-flex align-items-center">
                    <img src="{{ asset('icon/logo.png') }}" alt="" class="logo-header">
                    <div class="change-text-header-size text-left custom-text-size-header text-white font-weight-bold">
                        <span>Cục thuế thành phố Hồ Chí Minh</span><br>
                        <span>chi cục thuế quận 8</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark py-0" style="background-color: #2e8ff7;">
        <div class="container-fluid">
            <a class="navbar-brand" href={{ url('/') }}></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="col-lg-9">
                    <ul class="navbar-nav">
                        <li class="nav-item mx-1">
                            <a class="nav-link" href="{{ url('/') }}">Trang chủ</a>
                        </li>
                        <li class="nav-item mx-1 dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="GioiThieuDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Giới thiệu
                            </a>
                            <div class="dropdown-menu" aria-labelledby="GioiThieuDropdown">
                            </div>
                        </li>
                        <li class="nav-item mx-1">
                            <a class="nav-link" href="{{ url('news') }}">Bài đăng</a>
                        </li>
                        <li class="nav-item mx-1">
                            <a class="nav-link" href="#">Dịch vụ công</a>
                        </li>
                        <li class="nav-item mx-1">
                            <a class="nav-link" href="{{ url('documents') }}">Văn bản</a>
                        </li>
                        <li class="nav-item mx-2 dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="tienIchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Tiện ích
                            </a>
                            <div class="dropdown-menu" aria-labelledby="tienIchDropdown">
                                <a class="dropdown-item" href="#">Tool 1</a>
                                <a class="dropdown-item" href="#">Tool 2</a>
                                <a class="dropdown-item" href="#">Tool 3</a>
                            </div>
                        </li>
                        <li class="nav-item mx-1">
                            <a class="nav-link" href="{{ url('contact') }}">Liên hệ</a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-3">
                    <form class="d-flex">
                        <div class="input-group">
                            <input style="max-height: 30px" class="mt-2 form-control py-2 border-right-0 border w-55"
                                id="searchInput" type="search" placeholder="Search" aria-label="Search">
                        </div>
                    </form>
                    <div id="searchResult" class="mt-1"
                        style="position: absolute; background-color: white; z-index: 999;  display: none;">
                    </div>

                </div>
            </div>
        </div>
    </nav>
</header>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-9 d-flex justify-content-start align-items-center">
            <marquee class="text-run" direction="left" behavior="scroll" scrollamount="3">Minh bạch – Chuyên nghiệp –
                Liêm chính – Đổi mới</marquee>
        </div>
        <div class="col-lg-2 col-3 d-flex justify-content-end align-items-center">
            <p id="currentDateTime"></p>
        </div>
    </div>
</div>

<style>
    .searchResult {
        max-width: 300px;
        /* Đặt giới hạn chiều rộng tối đa cho kết quả tìm kiếm */
        margin-bottom: 10px;
        /* Tạo khoảng cách giữa các kết quả tìm kiếm */
        text-decoration: none;
        color: black;
    }


    .bannerimage {
        width: 100%;
        background-image: url({{ asset('imgs/header-bg.webp ') }});
        max-height: 200px;
        background-position: center;
    }

    .custom-text-size-header {
        font-weight: bold;
        font-size: 25px;
    }

    .navbar-nav .nav-item .nav-link {
        color: #fff;
        font-weight: bold;
        font-size: 18px;
    }

    .navbar-nav .nav-item.dropdown:hover .dropdown-menu {
        display: block;
    }

    .logo-header {
        height: 110px;
        width: 110px;
    }

    .text-run {
        font-size: 20px;
        color: black;
    }

    @media screen and (max-width: 900px) {
        .logo-header {
            height: 110px;
            width: 110px;
        }

        .change-text-header-size {
            font-size: 25px;
        }
    }

    @media screen and (max-width: 900px) {
        .logo-header {
            height: 110px;
            width: 110px;
        }

        .text-run {
            font-size: 20px;
        }

        .change-text-header-size {
            font-size: 25px;
        }
    }

    @media screen and (max-width: 600px) {
        .logo-header {
            height: 80px;
            width: 80px;
        }

        .text-run {
            font-size: 18px;
        }

        .change-text-header-size {
            font-size: 15px;
        }
    }
</style>
<script>
    function updateDateTime() {
        var currentDateTime = new Date();
        var day = currentDateTime.getDate();
        var month = currentDateTime.getMonth() + 1;
        var year = currentDateTime.getFullYear();
        var hour = currentDateTime.getHours();
        var minute = currentDateTime.getMinutes();
        var second = currentDateTime.getSeconds();

        document.getElementById("currentDateTime").innerHTML = day + "/" + month + "/" + year + " " + hour + ":" +
            minute + ":" + second;
    }

    // Cập nhật ngày và giờ mỗi giây
    setInterval(updateDateTime, 1000);

    // Đảm bảo cập nhật lần đầu tiên khi trang được tải
    updateDateTime();
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var parentName = "Giới thiệu"; // Tên của thể loại cha
        $.ajax({
            url: 'api/v1/all-categories',
            method: 'GET',
            success: function(data) {
                var dropdownMenu = $('#GioiThieuDropdown').next('.dropdown-menu');
                dropdownMenu.empty();
                $.each(data.data, function(index, subcategory) {
                    var url = '{{ url('news-category') }}/' + subcategory.id;
                    dropdownMenu.append('<a class="dropdown-item" href="' + url + '">' +
                        subcategory.name + '</a>');
                });
            }
        });
    });
</script>
<script>
    function displaySearchResults(results) {
        var html = '';
        if (results.length > 0) {
            results.forEach(function(item) {
                var truncatedTitle = item.title.length > 30 ? item.title.substring(0, 200) + '...' : item.title;
                html += '<div class="search-result-item">';
                html += '<a style="text-decoration: none !important;" href="{{ url('/detail/') }}/' + item.id + '" class="search-result-link">';
                html +=
                '<div class="d-flex align-items-center">'; // Thêm lớp d-flex và align-items-center vào đây
                html += '<div class="search-result-thumbnail">';
                html += '<img src="' + item.images + '" alt="' + item.title +
                    '" class="search-result-image" style="width: 50px; height: 50px;">';
                html += '</div>';
                html += '<div class="search-result-title" style="color: black; ">' +
                    truncatedTitle + '</div>';
                html += '</div>';
                html += '</a>';
                html += '</div>';
            });
        } else {
            html = '<a href="#" class="dropdown-item">No results found</a>';
        }
        $('#searchResult').html(html).show();
    }

    $(document).ready(function() {
        $('#searchInput').keyup(function() {
            var keyword = $(this).val();
            if (keyword.length > 0) { // Ensure we have at least 3 characters to start searching
                $.ajax({
                    url: "{{ url('/json/post.json') }}",
                    dataType: 'json',
                    success: function(data) {
                        var filteredData = data.filter(function(post) {
                            return post.title.toLowerCase().includes(keyword
                                .toLowerCase());
                        });
                        displaySearchResults(filteredData);
                    }
                });
            } else {
                $('#searchResult').empty().hide();
            }
        });
    });
</script>
