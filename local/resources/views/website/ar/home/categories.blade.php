@extends("website.{$locale}.layouts.master")

@section('content')
    <div class="mid-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="inner-heading">تصنيفات السلع التأجيرية</h3>
                </div>
            </div>
            <div class="row" >
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/002-plot.svg') }}">
                        </div>
                        <p class="app-name"> عقار </p>
                        <p class="app-description">(فلل وشقق، مستودعات، أراضي، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/001-heavy-machinery.svg') }}">
                        </div>
                        <p class="app-name">معدات ثقيلة</p>
                        <p class="app-description">(حاويات بناء، رافعات شوكية، وشيولات، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/003-combine-harvester.svg') }}">
                        </div>
                        <p class="app-name">معدات زراعية</p>
                        <p class="app-description">(حراثات، آلة حصاد، آلة تسميد، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/004-vehicle.svg') }}">
                        </div>
                        <p class="app-name">معدات تنظيف خارجي</p>
                        <p class="app-description">(جرافة، مكنسة صناعية، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/005-drill.svg') }}">
                        </div>
                        <p class="app-name">معدات كهربائية وسباكة</p>
                        <p class="app-description">(دريل، آلة قص مواسير، سلّم، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/006-treadmill-machine.svg') }}">
                        </div>
                        <p class="app-name">أجهزة رياضية ومستلزماتها</p>
                        <p class="app-description">(سير مشي، دراجات هوائية، أثقال، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/007-wheelchair.svg') }}">
                        </div>
                        <p class="app-name">أجهزة طبية ومستلزمات كبار السن</p>
                        <p class="app-description">(سرير طبي، كرسي متحرك، وأكثر)</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/008-tennis-racket.svg') }}">
                        </div>
                        <p class="app-name">ألعاب بالغين وألعاب أطفال</p>
                        <p class="app-description">(طاولة تنس وبلياردو، نطيطات، ملعب صابوني، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/009-game-console.svg') }}">
                        </div>
                        <p class="app-name">أجهزة الكمبيوتر وألعاب الفيدي وإلكترونيات وأكثر.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/010-book.svg') }}">
                        </div>
                        <p class="app-name">كتب</p>
                        <p class="app-description">(كتب أطفال، كتب جامعية، روايات، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/011-furniture.svg') }}">
                        </div>
                        <p class="app-name">أثاث منزلي ومكتبي</p>
                        <p class="app-description">(طاولات، سوفا وكراسي، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/012-conference.svg') }}">
                        </div>
                        <p class="app-name">الاحتفالات والمؤتمرات ومستلزماتها</p>
                        <p class="app-description">(قاعة زواجات ومؤتمرات، شاشات عرض وأنظمة صوت، كراسي وطاولات وزوالي، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/013-clothes-hanger.svg') }}">
                        </div>
                        <p class="app-name">ملابس وإكسسوارات للمناسبات</p>
                        <p class="app-description">(فساتين، بشوت، جاكيتات، وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/014-hair-dryer.svg') }}">
                        </div>
                        <p class="app-name">أجهزة تجميل ومستلزماتها</p>
                        <p class="app-description">(استشوار، ليزر منزلي، شنطة مكياج وأكثر).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="about-box">
                        <div class="abt-icon">
                            <img src="{{ asset('website/images/015-boat.svg') }}">
                        </div>
                        <p class="app-name">الرحلات ومستلزماتها</p>
                        <p class="app-description">(مخيمات، مواطير كهرباء ومواطير هواء، قوارب ودبابات، وأكثر).</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection