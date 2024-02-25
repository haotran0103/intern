<div>

    @extends('layout')
    <link rel="icon" href="{{ URL::asset('icon/logo.png') }}" type="image/x-icon">
    @section('layout')
        @include('client.header.index')

        <body class="d-flex flex-column">
            <div class="content-wrapper">
                @yield('content')
            </div>
        </body>
        @include('client.footer.index')
        @include('client.chatbox.index')
    @endsection

</div>

<style>
    .content-wrapper {
        width: 90%;
        margin: 0 auto;
    }

    @media (max-width: 500px) {
        .content-wrapper {
            width: 100%;
        }
    }
</style>
