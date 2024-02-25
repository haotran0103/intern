<link rel="stylesheet" href="https://datatables.net/1.13.7/css/jquery.dataTables.min.css">

@extends('admin.layout')
@section('content')
    <div class="container mt-5 mb-3">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h3>Quản lí Bài viết</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="bi bi-plus-circle"></i> Thêm mới
                    </button>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <table class="table" id="myTable">
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
            <tbody id="table-body">

            </tbody>
        </table>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm bài viết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-post">
                        @csrf
                        <div class="mb-3">
                            <label for="post-title" class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" id="post-title" name="title"
                                placeholder="Tiêu đề" />
                        </div>
                        <div class="mb-3">
                            <label for="post-short-desc" class="form-label">Mô tả ngắn</label>
                            <textarea class="form-control" id="post-short-desc" name="short_desc" placeholder="Mô tả ngắn"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="post-category" class="form-label">Danh mục</label>
                            <select class="form-select" id="post-category" name="category_id">
                                <!-- Populate categories dynamically using JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="post-image" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="post-image" name="image"
                                onchange="previewImage(this)" />
                            <img id="image-preview" class="mt-2" style="max-width: 100%;" />
                        </div>
                        <div class="mb-3">
                            <label for="post-serial-number" class="form-label">Số serial</label>
                            <input type="text" class="form-control" id="post-serial-number" name="serial_number"
                                placeholder="Số serial" />
                        </div>
                        <div class="mb-3">
                            <label for="post-issuance-date" class="form-label">Ngày phát hành</label>
                            <input type="date" class="form-control" id="post-issuance-date" name="Issuance_date" />
                        </div>
                        <div class="mb-3">
                            <textarea class="summernote" name="content"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="post-file" class="form-label">File</label>
                            <input type="file" class="form-control" id="post-file" name="file[]" multiple />
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
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Sửa bài viết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-post-form">
                        @csrf
                        <div class="mb-3">
                            <input type="hidden" id="edit-post-id" name="id" />
                            <label for="edit-post-title" class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" id="edit-post-title" name="title"
                                placeholder="Tiêu đề" />
                        </div>
                        <div class="mb-3">
                            <label for="edit-post-short-desc" class="form-label">Mô tả ngắn</label>
                            <textarea class="form-control" id="edit-post-short-desc" name="short_desc" placeholder="Mô tả ngắn"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit-post-category" class="form-label">Danh mục</label>
                            <select class="form-select" id="edit-post-category" name="category_id">
                                <!-- Populate categories dynamically using JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="post-image" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="post-image" name="image"
                                onchange="previewImage(this)" />
                            <img id="edit-post-image" class="mt-2" style="max-width: 100%;" />
                        </div>
                        <div class="mb-3">
                            <label for="edit-post-serial-number" class="form-label">Số serial</label>
                            <input type="text" class="form-control" id="edit-post-serial-numbe" name="serial_number"
                                placeholder="Số serial" />
                        </div>
                        <div class="mb-3">
                            <label for="edit-post-issuance-date" class="form-label">Ngày phát hành</label>
                            <input type="date" class="form-control" id="edit-post-issuance-date"
                                name="Issuance_date" />
                        </div>
                        <div class="mb-3">
                            <textarea id="edit-summernote" class="summernote" name="content"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit-post-file" class="form-label">File</label>
                            <input type="file" class="form-control" id="edit-post-file" name="file[]" multiple />
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
                    <h5 class="modal-title" id="btnModalModalLabel">Hành động bài viết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <button class="btn btn-primary change-status-button" data-id="' + row.id + '"
                        style="color: white;">Chuyển trạng thái</button>
                    <button class="btn btn-warning edit-button" data-bs-toggle="modal" data-bs-target="#editModal"
                        data-id="' + row.id + '" style="margin-right: 3px;color: white;">Sửa</button>
                    <button class="btn btn-danger delete-button" data-id="' + row.id + '"
                        style="margin-right: 3px;color: white;">Xóa</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Xóa Bài Viết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="edit-post-id" name="postID" />
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa bài viết này không?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '.delete-button', function() {
            var postId = $(this).data('id');
            $('#deleteModal').modal('show');
            $('#confirmDelete').data('id', postId); // Attach post ID to confirm button
        });

        $('#confirmDelete').on('click', function() {
            var postId = $(this).data('id');
            makeRequest('/api/v1/post/' + postId, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                }
            }).then(function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    alert('Xóa bài viết thành công!');
                    $('#myTable').DataTable().ajax.reload();
                } else {
                    alert('Có lỗi xảy ra, không thể xóa bài viết.');
                }
            }).catch(function(error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, không thể xóa bài viết.');
            });
        });
    </script>
@endsection
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js" defer></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.summernote').summernote();
    });
</script>
<script>
    function previewImage(input) {
        var file = input.files[0];

        if (file) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result);
            };

            reader.readAsDataURL(file);
        }
    }
</script>

<script>
    $(document).ready(function() {
        var table = $('#myTable').DataTable({
            ajax: {
                url: '/api/v1/post',
                type: 'GET',
                beforeSend: function(request) {
                    request.setRequestHeader('Authorization', 'Bearer ' + sessionStorage.getItem(
                        'accessToken'));
                },
                dataSrc: function(response) {
                    return response.data;
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'title'
                },
                {
                    data: 'short_desc',
                    render: function(data, type, row) {
                        if (type === 'display' && data.length > 100) {
                            return data.substr(0, 100) + '...';
                        }
                        return data;
                    }
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
        fetch('/api/v1/category')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const categoryForm = document.getElementById('post-category');
                    categoryForm.innerHTML = '';
                    categoryForm.innerHTML = '<option value="0">Chọn thể loại</option>';
                    categoryRecusive(data.category, 0, categoryForm);
                })
                .catch(error => {
                    console.log('There was a problem with the fetch operation: ' + error.message);
                });

            function categoryRecusive(data, parentId, dropdown, selectedCategoryId, text = '') {
                data.forEach(value => {
                    if (value.parent_id == parentId) {
                        const option = document.createElement('option');
                        option.value = value.id;
                        option.name = 'category_id';
                        option.textContent = text + value.name;
                        // Nếu id thể loại bằng với id thể loại của bài viết, chọn thể loại đó
                        if (value.id === selectedCategoryId) {
                            option.selected = true;
                        }
                        dropdown.appendChild(option);

                        categoryRecusive(data, value.id, dropdown, selectedCategoryId, text + '--');
                    }
                });
                dropdown.name = 'category_id';
            }
            if (window.matchMedia('(min-width: 768px)').matches) {
        $('#myTable tbody').on('dblclick', 'tr', function() {
            var rowData = table.row(this).data();
            if (rowData) {
                // Kiểm tra nếu dòng được nhấp có dữ liệu (không phải dòng trống)
                populateEditForm(rowData);
                $('#btnModal').modal('show');
                $('#btnModal').data('rowData', rowData);
            }
        });
    } else {
        // Nếu màn hình nhỏ hơn md, sử dụng sự kiện click
        $('#myTable tbody').on('click', 'tr', function() {
            var rowData = table.row(this).data();
            if (rowData) {
                // Kiểm tra nếu dòng được nhấp có dữ liệu (không phải dòng trống)
                populateEditForm(rowData);
                $('#btnModal').modal('show');
                $('#btnModal').data('rowData', rowData);
            }
        });
    }
        function populateEditForm(postData) {
            $('#edit-post-id').val(postData.id);
            $('#edit-post-title').val(postData.title);
            $('#edit-post-short-desc').val(postData.short_desc);
            if (postData.images) {
                $('#edit-post-image').attr('src', postData.images);
            }
            $('#edit-post-serial-number').val(postData.serial_number);
            $('#edit-post-issuance-date').val(postData.Issuance_date);
            $('#edit-summernote').summernote('code', postData.content);
            if (postData.file) {
                var fileList = JSON.parse(postData.file);
                var fileLinks = fileList.map(function(file) {
                    return '<a href="' + file + '" target="_blank">' + file + '</a>';
                });
                $('#edit-post-form #edit-file-list').html(fileLinks.join('<br>'));
            }

            fetch('/api/v1/category')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const categoryForm = document.getElementById('edit-post-category');
                    categoryForm.innerHTML = '';
                    categoryForm.innerHTML = '<option value="0">Chọn thể loại</option>';
                    categoryRecusive(data.category, 0, categoryForm, postData.category_id);
                })
                .catch(error => {
                    console.log('There was a problem with the fetch operation: ' + error.message);
                });

            function categoryRecusive(data, parentId, dropdown, selectedCategoryId, text = '') {
                data.forEach(value => {
                    if (value.parent_id == parentId) {
                        const option = document.createElement('option');
                        option.value = value.id;
                        option.name = 'category_id';
                        option.textContent = text + value.name;
                        // Nếu id thể loại bằng với id thể loại của bài viết, chọn thể loại đó
                        if (value.id === selectedCategoryId) {
                            option.selected = true;
                        }
                        dropdown.appendChild(option);

                        categoryRecusive(data, value.id, dropdown, selectedCategoryId, text + '--');
                    }
                });
                dropdown.name = 'category_id';
            }
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote();

        $('#add-post').submit(function(e) {
            $('.spinner').addClass('d-inline-block');
            e.preventDefault();
            var formData = new FormData(this);
            makeRequest('/api/v1/post', {
                method: 'POST',
                body: formData,
                headers: {
                    'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                }
            }).then(function(data) {
                $('.spinner').removeClass('d-inline-block');
                alert('Thêm bài post thành công.');
                $('#exampleModal').modal('hide');
                window.location.reload();
            }).catch(function(error) {
                $('.spinner').removeClass('d-inline-block');
                alert('Xãy ra lỗi trong quá trình thêm liên hệ với Quản lí để tiến hành sửa chữa.');
                console.log(error);
            });
        });

        $('#edit-post-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            let rowData = $('#btnModal').data('rowData');
            var postId = rowData.id
            makeRequest('/api/v1/updatePost', {
                method: 'POST',
                body: formData,
                headers: {
                    'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                }
            }).then(function(data) {
                console.log(data);
                $('#editModal').modal('hide');
                $('#myTable').DataTable().clear().draw();
                $('#myTable').DataTable().ajax.reload();
                alert('sửa bài viết thành công');
            }).catch(function(error) {
                alert('Xảy ra lỗi trong quá trình chỉnh sửa liên hệ với admin để tìm hiểu thêm');
                console.log(error);
            });
        });
        $('#btnModal').on('click', '.change-status-button', async function(e) {
            e.preventDefault();
            $('.spinner').addClass('d-inline-block');
            let rowData = $('#btnModal').data('rowData');
            try {
                const token = sessionStorage.getItem('accessToken');
                const formData = new FormData();
                formData.append('id', rowData.id);

                const response = await makeRequest('/api/v1/postStatus', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });
                console.log(response);
                if (response.message === 'success') {
                    $('.spinner').removeClass('d-inline-block');
                    $('#btnModal').modal('hide');
                    // Hiển thị thông báo thành công
                    alert('Chuyển trạng thái bài viết thành công.');
                    // Làm mới bảng sau khi thực hiện xong
                    $('#myTable').DataTable().clear().draw();
                    $('#myTable').DataTable().ajax.reload();
                } else {
                    $('.spinner').removeClass('d-inline-block');
                    console.error('Có lỗi xảy ra:', response.error);
                    alert('Đã xảy ra lỗi khi chuyển trạng thái bài viết!');
                }
            } catch (error) {
                // Hiển thị thông báo lỗi
                console.error('Có lỗi xảy ra:', error.message);
                alert('Đã xảy ra l��i khi chuyển trạng thái bài viết!');
            }
        });
        $('#deleteModal').on('click', '#confirmDelete', function() {
            let rowData = $('#btnModal').data('rowData');
            var postId = rowData.id
            $('.spinner').addClass('d-inline-block');
            makeRequest('/api/v1/post/' + postId, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                }
            }).then(function(response) {
                $('.spinner').removeClass('d-inline-block');
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    alert('Xóa bài viết thành công!');
                    $('#myTable').DataTable().clear().draw();
                    $('#myTable').DataTable().ajax.reload();
                } else {
                    alert('Có lỗi xảy ra, không thể xóa bài viết.');
                }
            }).catch(function(error) {
                $('.spinner').removeClass('d-inline-block');
                console.error('Error:', error);
                alert('Có lỗi xảy ra, không thể xóa bài viết.');
            });
        });
        function previewImage(input) {
            var file = input.files[0];

            if (file) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#edit-post-image').attr('src', e.target.result);
                };

                reader.readAsDataURL(file);
            }
        }
    });
</script>
