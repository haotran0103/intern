<!-- Trong file category.blade.php -->
<ul class="list-group">
    @foreach($categories as $category)
        <li class="list-group-item">
            <a href="#collapse_{{ $category->id }}" class="parent-category" data-toggle="collapse">{{ $category->name }}</a>
            <div id="collapse_{{ $category->id }}" class="collapse">
                @if($category->children->isNotEmpty())
                    @include('partials.category', ['categories' => $category->children])
                @endif
            </div>
        </li>
    @endforeach
</ul>
