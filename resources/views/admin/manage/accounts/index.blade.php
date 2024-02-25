@extends('admin.layout')
@section('content')
    <div class="container mt-5 mb-3">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <div>
                    <h3>Quản lí tài khoản</h3>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="bi bi-file-plus-fill">Thêm mới</i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <table class="table" id="myTable">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Tên</th>
                <th scope="col">Email</th>
                <th scope="col">Số điện thoại</th>
                <th scope="col">Quyền</th>
                <th scope="col">Trạng thái</th>
            </tr>
        </thead>
        <tbody id="table-body">

        </tbody>
    </table>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm tài khoản mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-user">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Tên" />
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" />
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="phone" name="phone"
                                placeholder="Số điện thoại" />
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="password" name="password"
                                placeholder="mật khẩu" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-warning float-end">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="btnModal" tabindex="-1" aria-labelledby="btnModalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="btnModalModalLabel">Các hành động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-category">
                        <button class="btn btn-warning edit-button" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-id="' + row.id + '" style=" margin-right: 3px;color: white;">Đổi trạng thái</button>
                        <button class="btn btn-danger delete-button" data-id="' + row.id + '"
                            style="margin-right: 3px; color: white;">Xóa</button>
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
        var table;

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
                    // Store the new tokens
                    sessionStorage.setItem('accessToken', data.access_token);
                    sessionStorage.setItem('refreshToken', data.refresh_token);
                    return data.access_token;
                });
        }

        function makeRequest(url, options) {
            return fetch(url, options)
                .then(response => {
                    if (response.status === 401) {
                        // Token has expired, try to refresh it
                        return refreshToken().then(newToken => {
                            // Retry the original request with the new token
                            options.headers['Authorization'] = `Bearer ${newToken}`;
                            return fetch(url, options);
                        });
                    } else {
                        // Token is still valid, process the response
                        return response.json();
                    }
                });
        }

        $(document).ready(function() {
            table = $('#myTable').DataTable({
                ajax: function(data, callback, settings) {
                    makeRequest('/api/v1/user', {
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + sessionStorage.getItem(
                                'accessToken')
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
                        data: 'phone'
                    },
                    {
                        data: 'role'
                    },
                    {
                        data: 'status'
                    }
                ]
            });
        });

        $('#myTable tbody').on('dblclick', 'tr', function() {
            var rowData = table.row(this).data();
            $('#btnModal').modal('show');
            // Lưu dữ liệu hàng trong modal
            $('#btnModal').data('rowData', rowData);
        });

        // Xử lý khi nhấp vào nút chỉnh sửa trong modal
        $('#btnModal .edit-button').on('click', async function(e) {
            e.preventDefault();
            // Lấy dữ liệu hàng từ modal
            var rowData = $('#btnModal').data('rowData');
            try {
                const token = sessionStorage.getItem('accessToken');
                const formData = new FormData();
                formData.append('id', rowData.id);

                const response = await makeRequest('/api/v1/userStatus', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });
                console.log(response);
                if (response.message === 'success') {
                    $('#btnModal').modal('hide');
                    // toastr.success('Thêm thể loại thành công!');
                    $('#myTable').DataTable().clear().draw();
                    $('#myTable').DataTable().ajax.reload();
                } else {
                    console.error('There was a problem with the fetch operation:', response.error);
                    // toastr.error('Thêm thể loại thất bại!');
                }
            } catch (error) {
                console.error('There was a problem with the fetch operation:', error.message);
                // toastr.error('Thêm thể loại thất bại!');
            }
        });
        $('#btnModal .delete-button').on('click', async function(e) {
            e.preventDefault();
            // Lấy dữ liệu hàng từ modal
            var rowData = $('#btnModal').data('rowData');
            try {
                const token = sessionStorage.getItem('accessToken');
                const response = await makeRequest(`/api/v1/restore/${rowData.id}`, {
                    method: 'put',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });
                console.log(response);
                if (response.message === 'success') {
                    $('#btnModal').modal('hide');
                    // toastr.success('Thêm thể loại thành công!');
                    $('#myTable').DataTable().clear().draw();
                    $('#myTable').DataTable().ajax.reload();
                } else {
                    console.error('There was a problem with the fetch operation:', response.error);
                    // toastr.error('Thêm thể loại thất bại!');
                }
            } catch (error) {
                console.error('There was a problem with the fetch operation:', error.message);
                // toastr.error('Thêm thể loại thất bại!');
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('add-user').addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            try {
                const token = sessionStorage.getItem('accessToken')
                const response = await makeRequest('/api/v1/auth/register', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });
                console.log(response);
                if (response.message === 'success') {
                    $('#exampleModal').modal('hide');
                    // toastr.success('Thêm thể loại thành công!');
                    $('#myTable').DataTable().clear().draw();
                    $('#myTable').DataTable().ajax.reload();
                } else {
                    console.error('There was a problem with the fetch operation:', response.error);
                    // toastr.error('Thêm thể loại thất bại!');
                }
            } catch (error) {
                console.error('There was a problem with the fetch operation:', error.message);
                // toastr.error('Thêm thể loại thất bại!');
            }
        });
    });
</script>
