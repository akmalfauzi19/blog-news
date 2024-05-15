   @extends('layouts-user.main')

   @section('content')
       <div class="row">
           <div class="col-lg-8 mb-5 mb-lg-0">
               <div class="blog_left_sidebar">
                   @forelse ($data as $article)
                       <article class="blog_item">
                           <div class="blog_item_img">
                               <img class="card-img rounded-0" src="{{ asset($article->image) }}" alt="{{ $article->title }}">
                               <a href="#" class="blog_item_date">
                                   @php
                                       $date = \Carbon\Carbon::parse($article->date_publish);
                                   @endphp
                                   <h3>{{ $date->format('d') }}</h3>
                                   <p>{{ $date->format('M') }}</p>
                               </a>
                           </div>
                           <div class="blog_details">
                               <a class="d-inline-block" href="{{ route('news.detail', $article->slug) }}">
                                   <h2>{{ $article->title }}</h2>
                               </a>
                               <ul class="blog-info-link">
                                   <li>
                                       <a href="{{ route('news.detail', $article->slug) }}">
                                           <i class="fa fa-user"></i>
                                           {{ $article->category->name }}
                                       </a>
                                   </li>

                               </ul>
                           </div>
                       </article>
                   @empty
                       <article class="blog_item" style="text-align: center;">
                           <h1>Article Tidak ada</h1>
                       </article>
                   @endforelse

                   <nav class="blog-pagination justify-content-center d-flex">
                       {{ $data->links('pagination::bootstrap-4') }}
                   </nav>
               </div>
           </div>
           <div class="col-lg-4">
               @include('page.users.blog-sidebar', [
                   'categories' => $categories,
               ])
           </div>
       </div>
   @endsection

   @push('scripts')
       <script>
           $(document).ready(function() {
               $('.category-news').click(function(e) {
                   e.preventDefault();
                   var category = $(this).attr('data-category');
                   var query = '{{ request()->input('query') }}';

                   var url = new URL(window.location.href);
                   url.searchParams.set('category', category);
                   if (query) {
                       url.searchParams.set('query', query);
                   }

                   window.location.href = url.toString();
               });
           });
       </script>
   @endpush
