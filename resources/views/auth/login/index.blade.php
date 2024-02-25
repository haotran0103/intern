@extends('auth.layout')

@section('content')
    <section class="vh-100" style="background-color: #afafaf;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">

                            <h3 class="mb-5">Đăng nhập admin</h3>

                            <form onsubmit="handleSubmit(); return false;">
                                @csrf

                                <div class="form-outline mb-4">
                                    <input placeholder="email" type="email" id="typeEmailX"
                                        class="form-control form-control-lg" />
                                </div>

                                <div class="form-outline mb-4">
                                    <input placeholder="Mật khẩu" type="password" id="typePasswordX"
                                        class="form-control form-control-lg" />
                                </div>

                                <div class="d-flex flex-column">
                                    <button class="btn btn-primary btn-lg btn-block mb-3" type="submit">Đăng nhập</button>
                                    <a style="text-decoration: none" href="{{ url('/') }}">Trở về trang chủ</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
<div class="modal fade" id="loginResultModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    async function handleSubmit() {
        const email = document.getElementById('typeEmailX').value;
        const password = document.getElementById('typePasswordX').value;

        if (!email.trim() || !password.trim()) {
            alert('Please enter valid email and password.');
            return;
        }

        try {
            const response = await fetch('{{ url('/api/v1/auth/login') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                }),
            });

            const data = await response.json();

            if (response.ok) {
                sessionStorage.setItem('accessToken', data.access_token);
                sessionStorage.setItem('refreshToken', data.refresh_token);
                sessionStorage.setItem('role', data.role);
                $('#loginResultModal').find('.modal-content').html(
                    '<div class="modal-header bg-success text-white"><h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-check2-circle"></i> Đăng nhập thành công</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body">...</div>'
                    );
                $('#loginResultModal').modal('show');
                const headers = {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${data.access_token}`
                };
                const dashboardResponse = await fetch('{{ url('/dashboard') }}', {
                    method: 'GET',
                    headers
                });
                if (dashboardResponse.ok) {
                    window.location.href = '{{ url('/dashboard') }}';
                } else {
                    console.error('Failed to load dashboard:', dashboardResponse);
                }
            } else {
                $('#loginResultModal').find('.modal-content').html(
                    '<div class="modal-header bg-danger text-white"><h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-exclamation-triangle"></i> Sai tài khoản hoặc mật khẩu</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body">Thử lại</div>'
                    );
                $('#loginResultModal').modal('show');
                console.error('Login failed:', data);
            }
        } catch (error) {
            console.error('Error during login:', error);
            // Handle error
        }
    }
</script>
