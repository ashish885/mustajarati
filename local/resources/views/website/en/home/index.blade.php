@extends("website.{$locale}.layouts.master")

@section('content')
    <section class="about-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p class="abt-box">WHY MUSTAJARATI?</p>
                    <p class="primary-text bold">About the Application</p>
                </div>
            </div>
            <div class="row" data-aos="zoom-in">
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon1.svg') }}" alt="" />
                        </div>
                        <p class="app-name">Ease of use and browsing:</p>
                        <p class="app-description">Mustajarati is easy to use by selecting products and services, and filtering results by category, location, type, and more.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon2.svg') }}" alt="" />
                        </div>
                        <p class="app-name">Various interface features:</p>
                        <p class="app-description">Mustajarati has an easy and dynamic application interface that helps the lessor to list their products, monitor them, know their status and the money earned.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon3.svg') }}" alt="" />
                        </div>
                        <p class="app-name">Adding services:</p>
                        <p class="app-description">Mustajarati provides service providers with a page to add their services and build their profile to market their services.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon4.svg') }}" alt="" />
                        </div>
                        <p class="app-name">Electronic payment:</p>
                        <p class="app-description">Mustajarati allows multiple electronic payment methods to use the service safely, easily and conveniently.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon5.svg') }}" alt="" />
                        </div>
                        <p class="app-name">Safety and reliability:</p>
                        <p class="app-description">To ensure the quality of service, Mustajarati ensures that the best security standards are applied, through: authenticating user accounts and verifying their identities, providing a confirmation code for the delivery and receipt of the product/service.</p>
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon6.svg') }}" alt="" />
                        </div>
                        <p class="app-name">Quality of delivery:</p>
                        <p class="app-description">The customer can order anything of his choice in an easy way of delivery within the city.</p>
                    </div>
                </div> -->
            </div>
        </div>
    </section>

    <section class="wave-outer-section">
        <img src="{{ URL::to('website/images/pattern_bottom.svg') }}" class="wave-top" alt="" />
        <section class="feature-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12" data-aos="zoom-in">
                        <p class="ft-box">FEATURES</p>
                        <p class="primary-text bold">Mustajarati features</p>
                    </div>
                </div>

                <div class="row flex-row mt-5" data-aos="zoom-in">
                    <div class="col-md-6">
                        <img src="{{ URL::to('website/images/features_image.png') }}" alt="" />
                    </div>
                    <div class="col-md-6">
                        <div class="feature-list-box">
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <img src="{{ URL::to('website/images/icon1.svg') }}" alt="" />
                                </div>
                                <div class="feature-text">
                                    <p class="feature-name">Earn Money</p>
                                    <p class="app-description">Giving users the investment opportunity to earn money by renting out their personal stuff.
</p>
                                </div>
                            </div>
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <img src="{{ URL::to('website/images/icon2.svg') }}" alt="" />
                                </div>
                                <div class="feature-text">
                                    <p class="feature-name">Nominal Prices</p>
                                    <p class="app-description">Fulfilling the daily, temporary and emergency needs of the users at nominal prices by leasing instead of buying.
</p>
                                </div>
                            </div>
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <img src="{{ URL::to('website/images/icon3.svg') }}" alt="" />
                                </div>
                                <div class="feature-text">
                                    <p class="feature-name">Add Services</p>
                                    <p class="app-description">Giving users the opportunity to get paid by offering their services, through the platform to invest in their professional skills or practical experience to carry out work on behalf of others.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <img src="{{ URL::to('website/images/pattern_top.svg') }}" class="wave-bottom" alt="" />
    </section>

    @if ($testimonials->count())
        <section class="satisfied-users">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center" data-aos="zoom-in">
                        <p class="abt-box">Happy customers</p>
                        <p class="primary-text bold">Satisfied Users</p>
                    </div>

                    <div class="col-md-12 mt-2">
                        <div class="satisfied-slider row" data-aos="zoom-in">
                            @foreach ($testimonials as $row)
                                <div class="slider-box col-md-4">
                                    <div class="rate-box">
                                        <span class="font-16">
                                            @for ($i = 0; $i < $row->rating; $i++)
                                                <i class="fa fa-star yellow-color"></i>
                                            @endfor
                                        </span>
                                    </div>
                                    <p class="feature-name">{{ $row->{"{$ql}title"} }}</p>
                                    <p class="client-description">{{ $row->{"{$ql}description"} }}</p>
                                    <div class="client-info">
                                        <p class="client-name">{{ $row->customer_name }}</p>
                                        <p class="client-loc">{{ $row->{"{$ql}city"} }}</p>
                                    </div>
                                </div>
                            @endforeach          
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="wave-outer-section">
        <img src="{{ URL::to('website/images/pattern_bottom.svg') }}" class="wave-top" alt="" />
        <section class="feature-section footer-get-app">
            <img src="{{ URL::to('website/images/app_download_bg.svg') }}" class="get-app-bg" alt="" />
            <div class="container">
                <div class="row flex-row">
                    <div class="col-md-6 text-left" data-aos="zoom-in">
                        <p class="ft-box">download the app</p>

                        <p class="primary-text">Get it to Your</p>
                        <h1 class="secondry-bold-text">Smartphone</h1>

                        <div class="newsletterbox">
                            <form id="smsFrm" onsubmit="return false;">
                                @csrf
                                <div class="form-group form-text-box">
                                    <div class="input-group">
                                        <select name="dial_code" class="select-control" data-placeholder="Choose">
                                            <option value=""></option>
                                            @if ($dialCodes)
                                                @foreach ($dialCodes as $item)
                                                    <option value="{{ $item->dial_code }}" {{ $item->dial_code == '966' ? 'selected' : '' }}>{{ "+{$item->dial_code} ({$item->name})" }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <input type="text" name="mobile_no" placeholder="Mobile Number" class="form-control" />
                                    </div>
                                    <button id="send-app-link-btn" class="btn get-sms-btn" type="button">GET SMS</button>
                                </div>
                                <div class="send-sms-error error"></div>
                            </form>
                        </div>

                        <p class="or">or download from</p>
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
                    <div class="col-md-6 app-right-section">
                        <img src="{{ URL::to('website/images/app_download_screen.png') }}" data-aos="zoom-in" alt="" />
                    </div>
                </div>
            </div>
        </section>
        <img src="{{ URL::to('website/images/pattern_top.svg') }}" class="wave-bottom" alt="" />
    </section>
@endsection

@section('script')
    <script>
        AOS.init({
            duration: 1500,
        });
        $('#smsFrm [name="dial_code"]').select2({
            templateSelection: val => val.id ? `+${val.id}` : val.text,
        });

        $('.satisfied-slider').slick({
            dots: false,
            infinite: true,
            speed: 300,
            arrows: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            prevArrow: "<button type='button' class='slick-prev pull-left'><i class='fa fa-angle-left'></i></button>",
            nextArrow: "<button type='button' class='slick-next pull-right'><i class='fa fa-angle-right'></i></button>",
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: true
                    }
                }, {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                }, {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        $(document).on('click', '#send-app-link-btn', function (e) {
            e.preventDefault();
            let btn = $(this),
                loader = $('#smsFrm .send-sms-error');

            $.ajax({
                dataType: 'json',
                type: 'POST',
                url: "{{ route('website.send.download.mobile_app') }}",
                data: $('#smsFrm').serialize(),
                beforeSend: () => {
                    loader.html('');
                    btn.prop('disabled', true);
                },
                error: (jqXHR, exception) => {
                    btn.attr('disabled',false);
                    loader.html(formatErrorMessage(jqXHR, exception)).addClass('text-red');
                },
                success: response => {
                    btn.attr('disabled',false);
                    loader.html(response.message).removeClass('text-red').addClass('text-green');
                    $('#smsFrm')[0].reset();
                }
            });
        });
    </script>
@endsection