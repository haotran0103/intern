@extends('admin.layout')
@section('content')
    <div class="container mt-5 mb-3">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <div>
                    <h3>Quản lí thể loại</h3>
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
                <th scope="col">Thể loại</th>
                <th scope="col">Thể loại cha</th>
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
                    <h5 class="modal-title" id="exampleModalLabel">Thêm thể loại mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-category">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="category" name="name"
                                placeholder="tên thể loại" />
                        </div>
                        <div class="mb-3">
                            <select class="form-select" id="post-category" name="parent_id">

                            </select>
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
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Sửa thông tin thể loại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-category">
                        @csrf
                        <!-- Fields for editing -->
                        <div class="mb-3">
                            <input type="hidden" id="edit-category-id" name="id" />
                            <label for="edit-category-name" class="form-label">Tên thể loại</label>
                            <input type="text" class="form-control" id="edit-category-name" name="name" />
                        </div>
                        <div class="mb-3">
                            <label for="edit-category-parent" class="form-label">Thể loại cha</label>
                            <select class="form-select" id="edit-category-parent" name="parent_id"></select>
                        </div>
                        <!-- End of fields -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-warning">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="btnModal" tabindex="-1" aria-labelledby="btnModalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="btnModalModalLabel">Sửa thông tin thể loại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-category">
                        <button class="btn btn-warning edit-button" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-id="' + row.id + '" style=" margin-right: 3px;color: white;">Sửa</button>
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
        var table = $('#myTable').DataTable({
            ajax: {
                url: '/api/v1/category',
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
                    data: 'name'
                },
                {
                    data: 'parent_name'
                },
            ]
        });

        if (window.matchMedia('(min-width: 768px)').matches) {
        $('#myTable tbody').on('dblclick', 'tr', function() {
            var rowData = table.row(this).data();
            if (rowData) {
                // Kiểm tra nếu dòng được nhấp có dữ liệu (không phải dòng trống)
                loadEditForm(rowData);
                $('#btnModal').modal('show');
            }
        });
    } else {
        // Nếu màn hình nhỏ hơn md, sử dụng sự kiện click
        $('#myTable tbody').on('click', 'tr', function() {
            var rowData = table.row(this).data();
            if (rowData) {
                // Kiểm tra nếu dòng được nhấp có dữ liệu (không phải dòng trống)
                loadEditForm(rowData);
                $('#btnModal').modal('show');
            }
        });
    }

        function loadEditForm(data) {
            $('#edit-category-id').val(data.id);
            $('#edit-category-name').val(data.name);
            populateCategoryDropdown(data.parent_id);
        }

        function populateCategoryDropdown(selectedCategoryId) {
            fetch('/api/v1/category')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const categoryDropdown = document.getElementById('edit-category-parent');
                    categoryDropdown.innerHTML = '';

                    const currentCategoryParentId = selectedCategoryId || 0;
                    const currentCategoryParentOption = document.createElement('option');
                    currentCategoryParentOption.value = currentCategoryParentId;
                    currentCategoryParentOption.textContent = 'Chọn thể loại cha';
                    currentCategoryParentOption.selected = true;
                    categoryDropdown.appendChild(currentCategoryParentOption);

                    categoryRecusive(data.category, 0, categoryDropdown, currentCategoryParentId);
                })
                .catch(error => {
                    console.log('There was a problem with the fetch operation: ' + error.message);
                });
        }

        function categoryRecusive(data, parentId, dropdown, selectedCategoryId, text = '') {
            data.forEach(value => {
                if (value.parent_id == parentId) {
                    const option = document.createElement('option');
                    option.value = value.id;
                    option.name = 'parent_id';
                    option.textContent = text + value.name;

                    // Set the selected attribute for the current category's parent
                    if (value.id == selectedCategoryId) {
                        option.selected = true;
                    }

                    dropdown.appendChild(option);

                    categoryRecusive(data, value.id, dropdown, selectedCategoryId, text + '--');
                }
            });
            dropdown.name = 'parent_id';
        }

        $('#btnModal .edit-button').on('click', function(e) {
            e.preventDefault();
            var rowData = table.row($(this).closest('tr')).data();
            loadEditForm(rowData);
            $('#editModal').modal('show');
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
                categoryForm.innerHTML = '<option value="0">Chọn thể loại cha</option>';
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
                    option.textContent = text + value.name;
                    // Nếu id thể loại bằng với id thể loại của bài viết, chọn thể loại đó
                    if (value.id === selectedCategoryId) {
                        option.selected = true;
                    }
                    dropdown.appendChild(option);

                    categoryRecusive(data, value.id, dropdown, selectedCategoryId, text + '--');
                }
            });
            dropdown.name = 'parent_id';
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('add-category').addEventListener('submit', async function(event) {
            const formData = new FormData(event.target);
            try {
                const response = await makeRequest('/api/v1/category', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                    }
                });
                console.log(response);
                if (response.message === 'success') {
                    $('#exampleModal').modal('hide');
                    alert('thêm thể loại thành công');
                    window.location.reload();
                } else {

                    console.error('There was a problem with the fetch operation:', response.error);
                    // toastr.error('Thêm thể loại thất bại!');
                    alert('Thêm thể loại thất bại!');
                }
            } catch (error) {
                console.error('There was a problem with the fetch operation:', error.message);
                alert('Thêm thể loại thất bại!');
                // toastr.error('Thêm thể loại thất bại!');
            }
        });

        document.getElementById('edit-category').addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const id = formData.get(
                'id'); // Assuming the form contains an input field with name 'id'
            try {
                const response = await makeRequest(`/api/v1/categories/${id}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                    }
                });

                if (response.message === 'success') {
                    $('#editModal').modal('hide');
                    console.log(response.data);
                    $('#myTable').DataTable().clear().draw();
                    $('#myTable').DataTable().ajax.reload();
                    alert('Đã xảy ra lỗi khi chuyển trạng thái thể loại');
                    // toastr.success('Category updated successfully!');
                } else {
                    console.error('There was a problem with the fetch operation:', response.error);
                    alert('Đã xảy ra lỗi khi chuyển trạng thái thể loại');
                    // toastr.error('Failed to update category!');
                }
            } catch (error) {
                console.error('There was a problem with the fetch operation:', error.message);
                alert('Đã xảy ra l��i khi chuyển trạng thái thể loại');
                // toastr.error('Failed to update category!');
            }
        });
    });
</script>
