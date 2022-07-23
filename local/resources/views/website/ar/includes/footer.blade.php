<footer class="footer-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="footer-box">
                    <a href="{{ route('website.home') }}"> <img src="{{ URL::to('website/images/green-logo.svg') }}" class="footer-logo" alt=""> </a>
                    <p class="get-mob">احصل على التطبيق</p>
                    <div class="header-app-btn">
                        <div class="app-btn btn">
                            <a href="{!! $apple_store_url !!}" class="anchor-text" target="_blank"></a>
                            <div class="app-icon">
                                <img src="{{ URL::to('website/images/apple.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="app-btn ml-2 btn">
                            <a href="{!! $play_store_url !!}" class="anchor-text" target="_blank"></a>
                            <div class="app-icon">
                                <img src="{{ URL::to('website/images/google_play.svg') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="footer-box">
                    <h3>تطبيق المستخدم</h3>
                    <ul>
                        <li><a href="{{ route('website.about_us') }}">من نحن</a></li>
                        <li><a href="{{ route('website.terms_conditions') }}">الشروط و الأحكام</a></li>
                        <li><a href="{{ route('website.privacy_policy') }}">سياسة الخصوصية</a></li>
                        <li><a href="{{ route('website.cancellation_policy') }}">سياسة الاسترداد</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="footer-box">
                    <h3>تطبيق المُؤجر</h3>
                    <ul>
                        <li><a href="{{ route('website.vendor.about_us') }}">من نحن</a></li>
                        <li><a href="{{ route('website.vendor.terms_conditions') }}">الشروط و الأحكام</a></li>
                        <li><a href="{{ route('website.vendor.privacy_policy') }}">سياسة الخصوصية</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-3">
                <div class="footer-box">
                    <p>الحقوق محفوظة {{ date('Y') }} مُستأجراتي</p>
                    <ul class="social-links">
                        <li><a href="javascript:void();"><i class="fa fa-facebook-square"></i></a></li>
                        <li><a href="javascript:void();"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="javascript:void();"><i class="fa fa-twitter-square"></i></a></li>
                        <li><a href="javascript:void();"><i class="fa fa-linkedin-square"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>