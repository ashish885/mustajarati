@extends("website.{$locale}.layouts.master")

@section('content')
    <section class="about-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p class="abt-box">لماذا  مُستأجراتي?</p>
                    <p class="primary-text bold">عن التطبيق</p>
                </div>
            </div>
            <div class="row" data-aos="zoom-in">
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon1.svg') }}" alt="" />
                        </div>
                        <p class="app-name">سهولة الاستخدام والتصفح : </p>
                        <p class="app-description">تتميز "مستأجراتي" بسهولة الاستخدام وتحديد المنتجات والخدمات وتصفية النتائج حسب الموقع والنوع وغيرها.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon2.svg') }}" alt="" />
                        </div>
                        <p class="app-name">واجهة متنوعة المزايا :</p>
                        <p class="app-description">تمتاز "مستأجراتي" بواجهة تطبيق سهلة ودايناميكية تساعد المؤجر ومقدم الخدمة على إدراج منتجاته ومراقبتها ومعرفة حالتها والأموال المكتسبة.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon3.svg') }}" alt="" />
                        </div>
                        <p class="app-name">إضافة الخدمات :</p>
                        <p class="app-description">تتيح "مستأجراتي" لمقدمي الخدمة صفحة لإضافة خدماتهم وبناء الملف الشخصي الخاص بهم لتسويق خدماتهم.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon4.svg') }}" alt="" />
                        </div>
                        <p class="app-name">الدفع الإلكتروني :</p>
                        <p class="app-description">تتيح "مستأجراتي" طرق متعددة للدفع الإلكتروني لاستخدام الخدمة بأمان وبكل يسر وسهولة.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon5.svg') }}" alt="" />
                        </div>
                        <p class="app-name">الأمان والموثوقية :</p>
                        <p class="app-description">لضمان جودة الخدمة، تأكد "مستأجراتي" على تطبيق أفضل معايير الأمان، وذلك من خلال : توثيق حسابات المستخدمين والتحقق من هوياتهم، تقديم كود تأكيد تسليم واستلام المنتج/الخدمة.</p>
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ URL::to('website/images/icon6.svg') }}" alt="" />
                        </div>
                        <p class="app-name">جودة التوصيل:</p>
                        <p class="app-description">يُمكن للعميل طلب أي شيء من اختياره بطريقة توصيل سهلة داخل المدينة</p>
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
                        <p class="ft-box">ميزات منصة "مُستأجراتي"</p>
                        <p class="primary-text bold">من أهم مميزات "مستأجراتي"</p>
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
                                    <p class="feature-name">كسب الأموال </p>
                                    <p class="app-description">إتاحة الفرصة للمستخدمين في كسب الأموال والاستثمار بتأجير ممتلكاتهم الخاصة.</p>
                                </div>
                            </div>
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <img src="{{ URL::to('website/images/icon2.svg') }}" alt="" />
                                </div>
                                <div class="feature-text">
                                    <p class="feature-name">أسعار رمزية </p>
                                    <p class="app-description">قضاء احتياجات المستخدمين اليومية والمؤقتة والطارئة بأسعار رمزية عن طريق الإجارة بدلا من الشراء.</p>
                                </div>
                            </div>
                            <div class="feature-box">
                                <div class="feature-icon">
                                    <img src="{{ URL::to('website/images/icon3.svg') }}" alt="" />
                                </div>
                                <div class="feature-text">
                                    <p class="feature-name">عرض الخدمات </p>
                                    <p class="app-description">إتاحة الفرصة للمستخدمين في عرض خدماتهم بمقابل من خلال المنصة ليستثمر بمهاراتهم المهنية أو خبراتهم العملية للقيام بإنجاز الأعمال نيابة عن الغير.</p>
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
                        <p class="ft-box">حمل التطبيق من</p>

                        <p class="primary-text">احصل على التطبيق</p>
                        <h1 class="secondry-bold-text">في جهازك</h1>

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
                                        <input type="text" name="mobile_no" placeholder="رقم الجوال" class="form-control" />
                                    </div>
                                    <button id="send-app-link-btn" class="btn get-sms-btn" type="button">تأكيد</button>
                                </div>
                                <div class="send-sms-error error"></div>
                            </form>
                        </div>

                        <p class="or"> أو قم بتحميله من</p>
                        <div class="header-app-btn">
                            <div class="app-btn btn">
                                <a href="{!! $apple_store_url !!}" class="anchor-text" target="_blank"></a>
                                <div class="app-icon">
                                    <img src="{{ URL::to('website/images/apple.svg') }}" alt="" />
                                </div>
                                <div class="app-text">
                                    <p class="small-text mb-0">حمل التطبيق من</p>
                                    <p class="big-text mb-0">متجر أبل</p>
                                </div>
                            </div>
                            <div class="app-btn ml-2 btn">
                                <a href="{!! $play_store_url !!}" class="anchor-text" target="_blank"></a>
                                <div class="app-icon">
                                    <img src="{{ URL::to('website/images/google_play.svg') }}" alt="" />
                                </div>
                                <div class="app-text">
                                    <p class="small-text mb-0">حمل التطبيق من</p>
                                    <p class="big-text mb-0">متجر جوجل</p>
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