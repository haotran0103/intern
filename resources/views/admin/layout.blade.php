@extends('layout')

<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<link href="{{URL::asset('assets/css/styles.css')}}" rel="stylesheet" />
<link rel="icon" href="{{ URL::asset('icon/logo.png') }}" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
@section('layout')
        @include('admin.navbar.index')
        <div id="layoutSidenav">
                @include('admin.sidebar.index')
                <div id="layoutSidenav_content">
                        <main>
                                @yield('content')
                        </main>
                </div>   
        </div>
@endsection     
<script src="{{ asset('assets/js/auth.js') }}"></script>
<script src="{{URL::asset('assets/js/scripts.js')}}"></script>
