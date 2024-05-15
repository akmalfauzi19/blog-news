   @extends('layouts-user.main')

   @section('content')
       <div class="row">
           <div class="col-lg-12">
               <div class="row d-flex justify-content-between">
                   <div class="col-lg-3 col-md-3">
                       <div class="section-tittle mb-30">
                           <h3>Whats New</h3>
                       </div>
                   </div>
                   <div class="col-lg-6 col-md-6">
                       <div class="properties__button">
                           <!--Nav Button  -->
                           @include('layouts-user.nav')
                           <!--End Nav Button  -->
                       </div>

                   </div>
                   <div class="col-lg-3 col-md-3">
                       <div class="header-right-btn f-right d-none d-lg-block">
                           {{-- <i class="fas fa-search special-tag"></i> --}}
                           <div class="search-box">
                               <form action="#">
                                   <input type="text" class="form-control" placeholder="Search">
                               </form>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="row">
                   <div class="col-12">
                       <!-- Nav Card -->
                       <div class="tab-content" id="nav-tabContent">
                           <!-- card one -->
                           <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                               aria-labelledby="nav-home-tab">
                               <div class="whats-news-caption">
                                   <div class="row">
                                       <div class="col-lg-5 col-md-5">
                                           <div class="single-what-news mb-100">
                                               <div class="what-img">
                                                   <img src="users/img/news/whatNews1.jpg" alt="">
                                               </div>
                                               <div class="what-cap">
                                                   <span class="color1">Night party</span>
                                                   <h4><a href="#">Welcome To The Best Model Winner
                                                           Contest</a></h4>
                                               </div>
                                           </div>
                                       </div>
                                       <div class="col-lg-5 col-md-5">
                                           <div class="single-what-news mb-100">
                                               <div class="what-img">
                                                   <img src="users/img/news/whatNews2.jpg" alt="">
                                               </div>
                                               <div class="what-cap">
                                                   <span class="color1">Night party</span>
                                                   <h4><a href="#">Welcome To The Best Model Winner
                                                           Contest</a></h4>
                                               </div>
                                           </div>
                                       </div>
                                       <div class="col-lg-5 col-md-5">
                                           <div class="single-what-news mb-100">
                                               <div class="what-img">
                                                   <img src="users/img/news/whatNews3.jpg" alt="">
                                               </div>
                                               <div class="what-cap">
                                                   <span class="color1">Night party</span>
                                                   <h4><a href="#">Welcome To The Best Model Winner
                                                           Contest</a></h4>
                                               </div>
                                           </div>
                                       </div>
                                       <div class="col-lg-5 col-md-5">
                                           <div class="single-what-news mb-100">
                                               <div class="what-img">
                                                   <img src="users/img/news/whatNews4.jpg" alt="">
                                               </div>
                                               <div class="what-cap">
                                                   <span class="color1">Night party</span>
                                                   <h4><a href="#">Welcome To The Best Model Winner
                                                           Contest</a></h4>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>

                       </div>
                       <!-- End Nav Card -->
                   </div>
               </div>
           </div>

       </div>
   @endsection
