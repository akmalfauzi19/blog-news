  @extends('layouts-user.main')
  @push('styles')
      <style>
          .about-prea {
              overflow: hidden;
              word-wrap: break-word;
              max-width: 100%;
          }

          .about-prea img {
              max-width: 70%;
              height: auto;
          }
      </style>
  @endpush

  @section('content')
      <div class="row">
          <div class="col-lg-8 posts-list">
              <div class="single-post">
                  <div class="feature-img">
                      <img class="img-fluid" src="{{ asset($article->image) }}" alt="{{ $article->title }}"
                          style=" border-radius: 3%;">
                  </div>
                  <div class="blog_details">
                      <h2>
                          {{ $article->title }}
                      </h2>
                      <ul class="blog-info-link mt-3 mb-4">
                          <li>
                              <i class="fa fa-user"> </i>
                              {{ $article->category->name }}
                          </li>
                      </ul>
                      <div class="about-prea">
                          {!! html_entity_decode($article->content) !!}
                      </div>
                  </div>
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

                  var url = new URL("{{ route('news.index') }}");
                  url.searchParams.set('category', category);
                  if (query) {
                      url.searchParams.set('query', query);
                  }

                  window.location.href = url.toString();
              });
          });
      </script>
  @endpush
