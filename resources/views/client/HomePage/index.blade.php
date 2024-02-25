@extends('client.layout')
@section('content')

    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 col-md-8 order-md-1 order-2">
                    <div class="container-fluid mt-3 mb-5">
                        <div class="d-flex justify-content-between">
                            <h4 class="text-muted change-text-size">BÀI VIẾT NỔI BẬT</h4>
                            <a href="#" class="text-muted text-decoration-none change-text-size">xem thêm</a>
                        </div>

                        <hr style="margin: 0 0 20px 0">
                        <div class="row">
                            <div class="col-lg-6" id="top-posts">
                                <div class="card h-100">
                                    <a href="{{ route('post.detail', ['id' => $mostPopularPosts[0]->id]) }}"
                                        class="stretched-link text-muted text-decoration-none">
                                        <div class="text-center">
                                            <img class="card-img-top mx-auto mt-3 w-100"
                                                src="{{ $mostPopularPosts[0]->images }}" alt="Card image">
                                        </div>
                                        <div class="card-body text-dark">
                                            <h4 class="card-title special-card">{{ $mostPopularPosts[0]->title }}</h4>
                                            <p class="card-title special-card">
                                                {{ Str::limit($mostPopularPosts[0]->short_desc, 300) }}</p>
                                                <div class="card-body text-dark">
                                                    @if ($mostPopularPosts[0]->category)
                                                        <a class="categorylink" href="{{ route('news.category', ['id' => $mostPopularPosts[0]->category->id]) }}">
                                                            {{ $mostPopularPosts[0]->category->name }}
                                                        </a>
                                                        @php
                                                            $parentCategory = $mostPopularPosts[0]->category->parent;
                                                        @endphp
                                                        @while ($parentCategory)
                                                            <a class="categorylink" href="{{ route('news.category', ['id' => $parentCategory->id]) }}">
                                                                ,{{ $parentCategory->name }}
                                                            </a>
                                                            @php
                                                                $parentCategory = $parentCategory->parent;
                                                            @endphp
                                                        @endwhile
                                                    @endif
                                                </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="d-flex flex-column">
                                    <div class="p-2" id="other-posts">
                                        @foreach ($mostPopularPosts->slice(1) as $post)
                                            <div class="row bg-light">
                                                <div class="d-inline-flex">
                                                    <div class="col-lg-3">
                                                        <img class="w-100" style="max-width: 250px; height: auto;"
                                                            src="{{ $post->images }}" alt="">
                                                    </div>
                                                    <a class="textPost col-lg-9"
                                                        href="{{ route('post.detail', ['id' => $post->id]) }}">
                                                        <p class="content-post">{{ Str::limit($post->title, 200) }}</p>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row bg-light mb-2 ">
                                                <div class="d-inline-flex">
                                                @if ($post->category)
                                                    <a class="categorylink"
                                                        href="{{ route('news.category', ['id' => $post->category->id]) }}">{{ $post->category->name }}</a>
                                                    @php
                                                        $parentCategory = $post->category->parent;
                                                    @endphp
                                                    @while ($parentCategory)
                                                        <a class="categorylink"
                                                            href="{{ route('news.category', ['id' => $parentCategory->id]) }}">,{{ $parentCategory->name }}</a>
                                                        @php
                                                            $parentCategory = $parentCategory->parent;
                                                        @endphp
                                                    @endwhile
                                                @endif
                                            </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 order-md-2 order-1">
                    <div class="container-fluid mt-3 mb-5 order-md-5 order-5">
                        <div class="d-flex justify-content-between">
                            <h5 class="change-text-size-other">Thông báo</h4>
                                <a href="#" class="text-muted text-decoration-none change-text-size-other">xem
                                    thêm</a>
                        </div>
                        <hr style="margin: 0 0 20px 0">
                        <div class="col-lg-12">
                            <div class="d-flex flex-column">
                                @foreach ($NotificationPosts as $post)
                                    <div class="p-2 bg-light mb-2">
                                        <div class="row">
                                            <div class="col-lg-12 d-flex align-items-center">
                                                <p style="font-size: 14px">{{ $post->title }}</p>
                                            </div>
                                            <div style="font-size: 12px">{{ $post->created_at }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <div class="container-fluid mt-1 mb-3 order-md-1 order-2">
                        <div class="d-flex justify-content-between">
                            <h4 class="text-muted change-text-size">Tin tức</h4>
                            <a href="#" class="text-muted text-decoration-none change-text-size">xem thêm</a>
                        </div>
                        <hr style="margin: 0 0 20px 0">
                        <div id="newsPost">
                            @foreach ($newsPost as $post)
                                <div class="row mt-1">
                                    <div class="p-2 bg-light">
                                        <div class="row">

                                            <div class="col-lg-10 align-items-center flex-column">
                                                <a class="textPost" href="{{ route('post.detail', ['id' => $post->id]) }}">
                                                    <div><b>{{ $post->title }}</b></div>
                                                    <div class="text-muted small mt-1 news-description">
                                                        {{ $post->short_desc }}
                                                    </div>
                                                </a>
                                            </div>

                                            <div class="col-lg-2">
                                                <img style="width: 100%; object-fit: cover" src="{{ $post->images }}"
                                                    alt="">
                                            </div>
                                        </div>
                                        @if ($post->category)
                                                    <a class="categorylink"
                                                        href="{{ route('news.category', ['id' => $post->category->id]) }}">{{ $post->category->name }}</a>
                                                    @php
                                                        $parentCategory = $post->category->parent;
                                                    @endphp
                                                    @while ($parentCategory)
                                                        <a class="categorylink"
                                                            href="{{ route('news.category', ['id' => $parentCategory->id]) }}">,{{ $parentCategory->name }}</a>
                                                        @php
                                                            $parentCategory = $parentCategory->parent;
                                                        @endphp
                                                    @endwhile
                                                @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="container-fluid mt-1 mb-3 order-md-4 order-4">
                        <div class="d-flex justify-content-between">
                            <h4 class="text-muted">Video hướng dẫn</h4>
                            <a href="#" class="text-muted text-decoration-none">xem thêm</a>
                        </div>
                        <hr style="margin: 0 0 20px 0">
                        <div class="row">
                            <div class="d-flex flex-column" id="videoPost">
                                <div class="col-12">
                                    @foreach ($VideoPosts as $videoPost)
                                        <div class="card w-100">
                                            <div class="video-container">
                                                {!! $videoPost->content !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <div class="container-fluid mt-1 mb-3 order-md-3 order-3">
                        <div class="d-flex justify-content-between">
                            <h4 class="text-muted change-text-size">Bài viết gần đây</h4>
                            <a href="#" class="text-muted text-decoration-none change-text-size">xem
                                thêm</a>
                        </div>
                        <hr style="margin: 0 0 20px 0">
                        <div class="row" id="latest-posts">
                            @foreach ($latestPosts as $post)
                                <div class="col-lg-3 col-md-6 col-sm-6 mb-3 col-6">
                                    <div class="card card-lastest-post h-100" style="max-width:200px;">
                                        <img class="card-img-top" style="height:140px; width:auto; object-fit: cover;"
                                            src="{{ $post->images }}" alt="Card image">
                                        <div class="card-body">
                                            <a class="textPost" href="{{ route('post.detail', ['id' => $post->id]) }}">
                                                <h4 style="font-size:16px" class="card-title">{{ Str::limit($post->title, 50) }}</h4>
                                                <p class="card-text lastestPost-description">{{ Str::limit($post->short_desc, 100) }}</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4">
        </div>
        </div>
        </div>
    </body>
@endsection
<style>
    .categorylink{
        text-decoration: none;
        color: black;
    }
    .textPost {
        text-decoration: none;
        color: black;
    }

    .video-container iframe {
        width: 100%;
        /* 100% chiều rộng để iframe điều chỉnh kích thước */
        height: 100%;
        /* 100% chiều cao để iframe điều chỉnh kích thước */
    }

    .card-lastest-post {
        width: 100%;
        max-width: 200px;
    }

    @media screen and (max-width: 900px) {
        .change-text-size-other {
            font-size: 20px;
        }

        .special-card {
            font-size: 20px;
        }
    }

    @media screen and (max-width: 800px) {
        .change-text-size-other {
            font-size: 15px;
        }

        .change-text-size {
            font-size: 20px;
        }
    }

    @media screen and (max-width: 500px) {
        .change-text-size-other {
            font-size: 13px;
        }

        .change-text-size {
            font-size: 17px;
        }
    }

    @media screen and (max-width: 600px) {
        .flex-column .row {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .special-card {
            font-size: 15px;
        }

        .flex-column .col-lg-2 {
            width: 100%;
            max-width: 100px;
        }

        .flex-column .col-lg-10 {
            width: 100%;
            max-width: calc(100% - 100px);
        }

        .change-text-size-other {
            font-size: 14px;
        }

        .special-card {
            font-size: 18px;
        }
    }

    @media screen and (max-width: 350px) {
        .change-text-size-other {
            font-size: 12px;
        }

        .text-content-post {

            font-size: 8px;
        }

        .special-card {
            font-size: 15px;
        }
    }
</style>


