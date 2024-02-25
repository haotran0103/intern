@extends('client.layout')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="accordion" id="categoryAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingAllCategories">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseAllCategories" aria-expanded="true"
                                aria-controls="collapseAllCategories">
                                Tất cả thể loại
                            </button>
                        </h2>
                        <div id="collapseAllCategories" class="accordion-collapse collapse show"
                            aria-labelledby="headingAllCategories" data-bs-parent="#categoryAccordion">
                            <div class="accordion-body">
                                @foreach ($categoriesWithChildren as $category)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $category['id'] }}">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $category['id'] }}" aria-expanded="true"
                                                aria-controls="collapse{{ $category['id'] }}">
                                                {{ $category['name'] }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $category['id'] }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $category['id'] }}"
                                            data-bs-parent="#collapseAllCategories">
                                            <div class="accordion-body">
                                                <ul class="list-group">
                                                    <li class="list-group-item" data-category-id="{{ $category['id'] }}">
                                                        <a href="#" class="category-link"
                                                            data-category-id="{{ $category['id'] }}">{{ $category['name'] }}</a>
                                                        <ul class="list-group">
                                                            @if (!empty($category['children']))
                                                                @foreach ($category['children'] as $child)
                                                                    <li class="list-group-item"
                                                                        data-category-id="{{ $child['id'] }}">
                                                                        <a href="#"
                                                                            class="category-link">{{ $child['name'] }}</a>
                                                                        @if (!empty($child['children']))
                                                                            <ul class="list-group">
                                                                                @foreach ($child['children'] as $grandchild)
                                                                                    <li class="list-group-item"
                                                                                        data-category-id="{{ $grandchild['id'] }}">
                                                                                        <a href="#"
                                                                                            class="category-link">{{ $grandchild['name'] }}</a>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12">
                <h2>Danh sách bài viết</h2>
                <div class="post-list" id="postList">
                    {{-- Dữ liệu bài viết được thêm bằng jQuery --}}
                </div>
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center mt-3">
                        <li class="page-item {{ $posts->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $posts->previousPageUrl() }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        @for ($i = 1; $i <= $posts->lastPage(); $i++)
                            <li class="page-item {{ $i == $posts->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $posts->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $posts->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $posts->nextPageUrl() }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <style>
        .video-container iframe {
            width: 100%;
            /* 100% chiều rộng để iframe điều chỉnh kích thước */
            height: 100%;
            /* 100% chiều cao để iframe điều chỉnh kích thước */
        }

        .list-group-item {
            cursor: pointer;
        }

        .list-group-item a {
            color: black;
            text-decoration: none;
        }

        /* Màu nền của collapse khi được mở */
        .accordion-item>.accordion-collapse {
            background-color: #fff;
            /* Màu nền mặc định */
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Hiển thị tất cả các bài viết từ dữ liệu trong biến $posts
            var postList = $('#postList');
            @foreach ($posts as $post) 
                var postHtml = `
        <div class="post bg-light mt-2 category-{{ $post->category_id }}">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-4">
                    @if ($post->images)
                        <a style="color: black; text-decoration: none;" href="{{ route('post.detail', ['id' => $post->id]) }}">
                            <img src="{{ asset($post->images) }}" alt="{{ $post->title }}" class="img-fluid">
                        </a>
                    @endif
                </div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-8">
                    <h5><a style="color: black; text-decoration: none;" href="{{ route('post.detail', ['id' => $post->id]) }}">{{ $post->title }}</a></h5>
                    <p>{{ Str::limit($post->short_desc, 150, '...') }}</p>
                </div>
            </div>
        </div>
    `;
                postList.append(postHtml);
            @endforeach
            // Xử lý sự kiện click trên các thẻ thể loại
            $('.category-link').click(function(e) {
                e.preventDefault();
                var categoryId = $(this).parent().data('category-id');
                filterPostsByCategory(categoryId);
            });
            // Hàm lọc bài viết theo thể loại
            function filterPostsByCategory(categoryId) {
                $.ajax({
                    url: '/api/v1/posts/filter/' + categoryId,
                    method: 'GET',
                    success: function(response) {
                        // Xử lý dữ liệu nhận được từ backend
                        if (response.data.length > 0) {
                            // Lặp qua từng bài viết và hiển thị trên giao diện
                            jQuery.each(response.data, function(index, post) {
                                var image_url = post.images;
                                var postHtml = `
                    <div class="post bg-light mt-2 category-${post.category_id}">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-4">
                                ${post.is_video ? `<div class="card w-100">
                                        <div class="video-container">
                                            ${post.content}
                                        </div>
                                    </div>` : `<a style="color: black; text-decoration: none;" href="/detail/${post.id}">
                                        <img src="{{ asset('` + image_url + `') }}" alt="${post.title}" class="img-fluid">
                                    </a>`}
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-8">
                                <h5><a style="color: black; text-decoration: none;" href="/detail/${post.id}">${post.title}</a></h5>
                                <p>${post.short_desc ? post.short_desc.substring(0, 150) + '...' : ''}</p>
                            </div>
                        </div>
                    </div>`;
                                postList.append(postHtml);
                            });
                        } else {
                            postList.append('<p>Hiện không có bài viết thuộc thể loại này.</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

        });
    </script>
@endsection
