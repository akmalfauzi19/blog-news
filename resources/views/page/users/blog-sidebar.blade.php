<div class="blog_right_sidebar">
    <aside class="single_sidebar_widget search_widget">
        <form action="{{ route('news.index') }}"method="GET">
            <div class="form-group">
                <div class="input-group mb-3">

                    <input type="text" class="form-control" name="query" placeholder='Search Keyword'
                        onfocus="this.placeholder = ''" onblur="this.placeholder = 'Search Keyword'"
                        value="{{ request()->input('query') }}">
                    <div class="input-group-append">
                        <button class="btns" type="submit">
                            <i class="ti-search"></i>
                        </button>
                    </div>

                </div>
            </div>
            @foreach (request()->except('query') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <button class="button rounded-0 primary-bg text-white w-100 btn_1 boxed-btn" type="submit">Search</button>
        </form>
    </aside>

    <aside class="single_sidebar_widget post_category_widget">
        <h4 class="widget_title">Category</h4>
        <ul class="list cat-list">
            <li>
                <a href="#" class="d-flex nav-link category-news" data-category="all">
                    <p>All</p>
                </a>
            </li>
            @forelse ($categories as $category)
                <li>
                    <a href="#" class="d-flex nav-link category-news" data-category="{{ $category->name }}">
                        <p>{{ ucwords($category->name) }}</p>
                    </a>
                </li>
            @empty
                <li style="text-align: center;">
                    <p>Tidak ada category</p>
                </li>
            @endforelse

        </ul>
    </aside>
</div>
