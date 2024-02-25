@extends('admin.layout')
@section('content')
    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#post">Bài viết</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#user">Người dùng</a>
        </li>

    </ul>
    <!-- Tab panes -->
    <div class="tab-content mt-5">
        <div class="tab-pane container active" id="post">
            <table class="table table-striped" id="postTable">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tiêu đề</th>
                        <th scope="col">Mô tả ngắn</th>
                        <th scope="col">Thể loại</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody id="post-table-body">

                </tbody>
            </table>
        </div>
        <div class="tab-pane container fade" id="user">
            <table class="table table-striped" id="userTable">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tên người dùng</th>
                        <th scope="col">Email</th>
                        <th scope="col">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="user-table-body">

                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="btnModal-post" tabindex="-1" aria-labelledby="btnModalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="btnModalModalLabel">Các hành động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-category">
                        <button class="btn btn-warning edit-button" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-id="' + row.id + '" style=" margin-right: 3px;color: white;">Khôi phục</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="btnModal-user" tabindex="-1" aria-labelledby="btnModalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="btnModalModalLabel">Các hành động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-category">
                        <button class="btn btn-warning edit-button" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-id="' + row.id + '" style=" margin-right: 3px;color: white;">Khôi phục</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" defer></script>
<script>
    $(document).ready(function() {
        // Hàm làm mới token
        function refreshToken() {
            return fetch('api/v1/auth/refresh', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${sessionStorage.getItem('accessToken')}`
                    },
                    body: JSON.stringify({
                        refresh_token: sessionStorage.getItem('refreshToken')
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Lưu lại các token mới
                    sessionStorage.setItem('accessToken', data.access_token);
                    sessionStorage.setItem('refreshToken', data.refresh_token);
                    return data.access_token;
                });
        }

        // Hàm thực hiện request với token mới
        function makeRequest(url, options) {
            return fetch(url, options)
                .then(response => {
                    if (response.status === 401) {
                        // Token hết hạn, thử làm mới
                        return refreshToken().then(newToken => {
                            // Thực hiện lại request ban đầu với token mới
                            options.headers['Authorization'] = `Bearer ${newToken}`;
                            return fetch(url, options);
                        });
                    } else {
                        // Token vẫn còn hạn, xử lý response
                        return response.json();
                    }
                });
        }

        // Khởi tạo bảng postTable
        var postTable = $('#postTable').DataTable({
            ajax: function(data, callback, settings) {
                makeRequest('/api/v1/trashed-posts', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                    }
                }).then(response => {
                    callback({
                        data: response.data
                    });
                }).catch(error => {
                    console.error('Error:', error);
                });
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'title'
                },
                {
                    data: 'short_desc'
                },
                {
                    data: 'category_name',
                    render: function(data, type, row) {
                        return data + ' - ' + row.parent_name;
                    }
                },
                {
                    data: 'status'
                },
            ]
        });

        // Khởi tạo bảng userTable
        var userTable = $('#userTable').DataTable({
            ajax: function(data, callback, settings) {
                makeRequest('/api/v1/trashed', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                    }
                }).then(response => {
                    callback({
                        data: response.data
                    });
                }).catch(error => {
                    console.error('Error:', error);
                });
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'status'
                },
            ]
        });


        $('#userTable tbody').on('dblclick', 'tr', function() {
            var rowData = userTable.row(this).data();
            $('#btnModal-user').modal('show');
            $('#btnModal-user').data('rowData', rowData);
        });
        $('#postTable tbody').on('dblclick', 'tr', function() {
            var rowData = postTable.row(this).data();
            $('#btnModal-post').modal('show');
            $('#btnModal-post').data('rowData', rowData);
        });


        $('#btnModal-user .edit-button').on('click', async function(e) {
            e.preventDefault();
            var rowData = $('#btnModal-user').data('rowData');
            try {
                const response = await fetch(`/api/v1/restore/${rowData.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${sessionStorage.getItem('accessToken')}`
                    }
                });
                console.log(response);
                if (response.message === 'success') {
                    $('#btnModal-user').modal('hide');
                    $('#userTable').DataTable().clear().draw();
                    $('#userTable').DataTable().ajax.reload();
                    alert('không phục user thành công')
                } else {
                    alert('không phục user thất bại liên hệ quản trị viên để biết thêm thông tin ')
                    console.error('There was a problem with the fetch operation:', response.error);
                }
            } catch (error) {
                alert('không phục user thất bại liên hệ quản trị viên để biết thêm thông tin ')
                console.error('There was a problem with the fetch operation:', error.message);
            }
        });
        $('#btnModal-post .edit-button').on('click', async function(e) {
            e.preventDefault();
            var rowData = $('#btnModal-post').data('rowData');
            try {
                const response = await fetch(`/api/v1/restore-posts/${rowData.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${sessionStorage.getItem('accessToken')}`
                    }
                });
                console.log(response);
                if (response.message === 'success') {
                    $('#btnModal-post').modal('hide');
                    $('#postTable').DataTable().clear().draw();
                    $('#postTable').DataTable().ajax.reload();
                    alert('khôi phục bài viết thành công!')
                } else {
                    alert('không phục bài viết thất bại liên hệ quản trị viên để biết thêm thông tin ')
                    console.error('There was a problem with the fetch operation:', response.error);
                }
            } catch (error) {
                alert('không phục bài viết thất bại liên hệ quản trị viên để biết thêm thông tin ')
                console.error('There was a problem with the fetch operation:', error.message);
            }
        });
    });
</script>
