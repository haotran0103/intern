@extends('admin.layout')
@section('content')
    <div class="container mt-5 mb-3">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <div>
                    <h3>Quản lí thông tin</h3>
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
                <th scope="col">Tên cấu hình</th>
                <th scope="col">Giá trị</th>
            </tr>
        </thead>
        <tbody id="table-body">

        </tbody>
    </table>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm thể loại mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-setting">
                        @csrf
                        <div class="mb-3">
                            <input type="text" class="form-control" id="name" name="config_key"
                                placeholder="Tên cấu hình" />
                        </div>
                        <div class="mb-3">
                            <label for="add-type">Loại</label>
                            <select class="form-control" id="add-type" name="add-type">
                                <option value="text">Text</option>
                                <option value="image">Ảnh</option>
                            </select>
                        </div>
                        <div class="mb-3" id="add-imageInput" style="display: none;">
                            <label for="image">Chọn ảnh</label>
                            <input type="file" class="form-control" id="add-image" name="config_value" accept="image/*">
                        </div>
                        <div class="mb-3" id="add-textInput">
                            <label for="add-setting-value" class="form-label">Giá trị</label>
                            <input type="text" class="form-control" id="add-setting-value" name="config_value" />
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
                    <h5 class="modal-title" id="editModalLabel">Sửa thông tin thể loại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-setting">
                        @csrf
                        <!-- Fields for editing -->
                        <div class="mb-3">
                            <input type="hidden" id="edit-setting-id" name="id" />
                            <label for="edit-setting-name" class="form-label">Tên Cấu hình</label>
                            <input type="text" class="form-control" id="edit-setting-name" name="config_key" />
                        </div>
                        <div class="mb-3">
                            <label for="edit-type">Loại</label>
                            <select class="form-control" id="edit-type" name="edit-type">
                                <option value="text">Text</option>
                                <option value="image">Ảnh</option>
                            </select>
                        </div>
                        <div class="mb-3" id="edit-imageInput" style="display: none;">
                            <label for="image">Chọn ảnh</label>
                            <input type="file" class="form-control" id="edit-image" name="config_value"
                                accept="image/*">
                        </div>
                        <div class="mb-3" id="edit-textInput">
                            <label for="edit-setting-value" class="form-label">Giá trị</label>
                            <input type="text" class="form-control" id="edit-setting-value" name="config_value" />
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
@endsection

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" defer></script>
<script>
    $(document).ready(function() {
        $('#edit-type').change(function() {
            var selectedType = $(this).val();
            if (selectedType === 'image') {
                $('#edit-imageInput').show();
                $('#edit-textInput').hide();

            } else {
                $('#edit-imageInput').hide();
                $('#edit-textInput').show();
            }
        });
        $('#add-type').change(function() {
            var selectedType = $(this).val();
            if (selectedType === 'image') {
                $('#add-imageInput').show();
                $('#add-textInput').hide();

            } else {
                $('#add-imageInput').hide();
                $('#add-textInput').show();
            }
        });
        var table;

        function refreshToken() {
            return fetch('api/auth/refresh', {
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
                    makeRequest('/api/v1/ReadSetting', {
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + sessionStorage.getItem(
                                'accessToken')
                        }
                    }).then(response => {
                        callback({
                            data: response
                        });
                    }).catch(error => {
                        console.error('Error:', error);
                    });
                },
                columns: [{
                        data: 'config_key'
                    },
                    {
                        data: 'config_value',
                        render: function(data, type, row) {
                            if (row.type === 'image') {
                                return '<img style="max-width:200px; object-fit:cover" src="' +
                                    data + '" alt="Image">';
                            } else {
                                return data;
                            }
                        }
                    }
                ]
            });
        });


        $('#myTable tbody').on('dblclick', 'tr', function() {
            var rowData = table.row(this).data();
            loadEditForm(rowData);
            $('#editModal').modal('show');
        });

        function loadEditForm(data) {
            $('#edit-setting-name').val(data.config_key);
            $('#edit-setting-value').val(data.config_value);
            $('#edit-setting-id').val(data.id);
        }

        document.getElementById('add-setting').addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            try {
                const response = await makeRequest('/api/v1/AddSetting', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                    }
                });
                console.log(response);
                $('#exampleModal').modal('hide');
                $('#myTable').DataTable().clear().draw();
                $('#myTable').DataTable().ajax.reload();
                // toastr.success('Category updated successfully!');
            } catch (error) {
                console.error('There was a problem with the fetch operation:', error.message);
                // toastr.error('Failed to update category!');
            }
        });

        document.getElementById('edit-setting').addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const id = formData.get('id');
            try {
                const response = await makeRequest(`/api/v1/UpdateSetting/${id}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
                    }
                });
                console.log(response);
                $('#editModal').modal('hide');
                $('#myTable').DataTable().clear().draw();
                $('#myTable').DataTable().ajax.reload();
                // toastr.success('Category updated successfully!');
            } catch (error) {
                console.error('There was a problem with the fetch operation:', error.message);
                // toastr.error('Failed to update category!');
            }
        });
    });
</script>
