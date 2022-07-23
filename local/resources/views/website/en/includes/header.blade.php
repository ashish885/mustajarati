@if (Route::is('website.home'))
    <div class="upper-header">
        <div class="home-header">
            <div class="container">
            <nav class="navbar navbar-default mobile_nav">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a href="{{ route('website.home') }}" class="logo-box"><img data-aos="zoom-in" src="{{ URL::to('website/images/top_logo.svg') }}"></a>
            <a class="btn lang-switch-btn pull-right" href="{{ route('website.change.locale', $switcherLocale) }}">{{ $switcherLocale == 'ar' ? 'Arabic' : 'English' }}</a>
                
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li>  <a href="{{ route('website.categories') }}" class="link-btn">Categories of rented items</a></li>
                <li> <a href="{{ route('website.contact_us') }}" class="link-btn">Contact Us</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
                <div class="row flex-row header-row desktop-nav">
               

                    <div class="col-md-6">
                        <a href="{{ route('website.home') }}" class="logo-box"><img data-aos="zoom-in" src="{{ URL::to('website/images/top_logo.svg') }}"></a>
                    </div>
                    <div class="col-md-6">
                        <a class="btn lang-switch-btn pull-right" href="{{ route('website.change.locale', $switcherLocale) }}">{{ $switcherLocale == 'ar' ? 'Arabic' : 'English' }}</a>
                        <a href="{{ route('website.contact_us') }}" class="link-btn">Contact Us</a>
                        <a href="{{ route('website.categories') }}" class="link-btn">Categories of rented items</a>
                    </div>
                </div>

                <div class="row flex-row p-50">
                    <div class="col-md-6">
                        <div class="header-text-box" data-aos="zoom-in">
                            <p class="mt-box">Mustajarati</p>
                            <p class="primary-text">Together, We own everything!</p>
                            <h1 class="secondry-bold-text">Mustajarati | Save, Rent, and Invest</h1>

                            <div class="header-app-btn">
                                <div class="app-btn btn">
                                    <a href="{!! $apple_store_url !!}" class="anchor-text" target="_blank"></a>
                                    <div class="app-icon">
                                        <img src="{{ URL::to('website/images/apple.svg') }}" alt="" />
                                    </div>
                                    <div class="app-text">
                                        <p class="small-text mb-0">Download on the</p>
                                        <p class="big-text mb-0">App Store</p>
                                    </div>
                                </div>
                                <div class="app-btn ml-2 btn">
                                    <a href="{!! $play_store_url !!}" class="anchor-text" target="_blank"></a>
                                    <div class="app-icon">
                                        <img src="{{ URL::to('website/images/google_play.svg') }}" alt="" />
                                    </div>
                                    <div class="app-text">
                                        <p class="small-text mb-0">Get it on</p>
                                        <p class="big-text mb-0">Google Play</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img src="{{ URL::to('website/images/header_device.png') }}" class="header-device pull-right" data-aos="zoom-in" alt="" />
                    </div>
                </div>
            </div>
        </div>
        <img src="{{ URL::to('website/images/pattern_top.svg') }}" alt="Wave Graphic" class="wave-bottom" />
    </div>
@else
    <div class="inner-header">
        <div class="container">
        <nav class="navbar navbar-default mobile_nav">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a href="{{ route('website.home') }}" class="logo-box"><img  src="{{ URL::to('website/images/top_logo.svg') }}"></a>
            <a class="btn lang-switch-btn pull-right" href="{{ route('website.change.locale', $switcherLocale) }}">{{ $switcherLocale == 'ar' ? 'Arabic' : 'English' }}</a>
                
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li> <a href="{{ route('website.contact_us') }}" class="link-btn">Contact Us</a></li>
              <li>  <a href="{{ route('website.categories') }}" class="link-btn">Categories of rented items</a></li>
            
            </ul>
         
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
            <div class="row flex-row header-row desktop-nav">
                <div class="col-md-6">
                    <a href="{{ route('website.home') }}" class="logo-box"><img src="{{ URL::to('website/images/top_logo.svg') }}"></a>
                </div>
                <div class="col-md-6">
                    <a class="btn lang-switch-btn pull-right" href="{{ route('website.change.locale', $switcherLocale) }}">{{ $switcherLocale == 'ar' ? 'Arabic' : 'English' }}</a>
                    <a href="{{ route('website.contact_us') }}" class="link-btn">Contact Us</a>
                    <a href="{{ route('website.categories') }}" class="link-btn">Categories of rented items</a>
                </div>
            </div>
        </div>
    </div>
@endif