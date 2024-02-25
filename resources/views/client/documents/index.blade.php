@extends('client.layout')
@section('content')
    <div class="container mt-4">
        @if ($posts->count() > 0)
            <div class="document-item mb-3">
                <div class="row">
                    @foreach ($posts as $post)
                        <div class="col-md-4">
                            <div class="col-md-3">
                                <img src="{{ asset($post->images) }}" alt="{{ $post->title }}" class="img-fluid">
                            </div>
                            <div class="col-md-9">
                                <h5><a href="/detail/{{ $post->id }}">{{ $post->title }}</a></h5>
                                <p>{{ $post->short_desc }}</p>
                                <div class="details">
                                    <p>ngày đăng: {{ $post->created_at->format('Y-m-d') }}</p>
                                </div>                                
                            </div>
                        </div>
                    @endforeach
                </div>
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
        @else
            <p>No documents found.</p>
        @endif
    </div>

@endsection
