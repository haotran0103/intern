@extends('client.layout')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-9 col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <div class="post-content">
                            <div class="text-center mb-3">
                                <h4 class="mt-4">{{$post->title}}</h4>
                            </div>
                            {!! $post->content !!}
                            <h5 class="card-title mt-3">Tài liệu đính kèm</h5>
                            @php
                                $files = json_decode($post->file, true);
                            @endphp

                            @if (!empty($files))
                                @foreach ($files as $file)
                                    @php
                                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                                    @endphp
                                    @if ($extension === 'pdf')
                                        <iframe src="{{ asset( $file) }}" style="width:100%; height:600px;"
                                            frameborder="0"></iframe>
                                    @elseif ($extension === 'docx' || $extension === 'doc')
                                        <iframe
                                            src="https://view.officeapps.live.com/op/embed.aspx?src={{ asset( urlencode($file)) }}"
                                            style="width:100%; height:600px;" frameborder="0"></iframe>
                                    @else
                                        <a href="{{ asset($file) }}" class="btn btn-primary">Tải xuống</a>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-muted">Không có tài liệu đính kèm</p>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin bài viết</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Ngày đăng: {{ $post->created_at->format('d/m/Y') }}</li>
                            <li class="list-group-item">Lượt xem: {{ $post->views }}</li>
                            <li class="list-group-item">Danh mục: {{ $post->category_name }}</li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="container mt-2">
        <div class="d-flex justify-content-between">
            <h4 class="text-muted">Video hướng dẫn</h4>
            <a href="#" class="text-muted text-decoration-none">xem thêm</a>
        </div>
        <hr style="margin: 0 0 20px 0">
        <div class="row" id="latest-posts">
            <!-- Loop through recent posts here -->
            @foreach ($relatedPosts as $post)
                <div class="col-lg-2 col-md-4 col-6 mb-2">
                    <div class="card  h-100" style="max-width:200px; object-fit: cover">
                        <img class="card-img-top" style="height:140px; width:auto;" src="{{ asset($post->images) }}"
                            alt="Card image">
                        <div class="card-body">
                            <h4 style="font-size:15px" class="card-title">{{ Str::limit($post->title, 40, '...') }}</h4>
                            <p class="card-text lastestPost-description">{{ Str::limit($post->short_desc, 100, '...') }}</p>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


@endsection
