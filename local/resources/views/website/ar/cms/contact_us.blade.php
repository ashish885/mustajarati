@extends("website.{$locale}.layouts.master")

@section('content')
    <div class="mid-content">
        <div class="container">
            <!-- <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="{{ route('website.home') }}">Home</a></li>
                        <li>Contact Us</li>
                    </ul>
                </div>
            </div> -->
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="contact-form">
                        <h3 class="inner-heading mb-5">أكتب لنا رسالة</h3>
                        <div class="message-box alert hide"></div>
                        <form id="contactFrm">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="name" placeholder="ادخل اسمك *" class="form-control" />
                            </div>
                            <div class="form-group">
                                <input type="text" name="email" placeholder="ادخل البريد الالكتروني *" class="form-control" />
                            </div>
                            <div class="form-group mobile-box">
                                <div class="coumtryCode">
                                    <select name="dial_code" class="select-control" data-placeholder="Choose">
                                        <option value=""></option>
                                        @if ($dialCodes)
                                            @foreach ($dialCodes as $item)
                                                <option value="{{ $item->dial_code }}" {{ $item->dial_code == '966' ? 'selected' : '' }}>{{ "+{$item->dial_code} ({$item->name})" }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <input type="number" name="mobile_no" placeholder="ادخل رقم الجوال *" class="form-control" />
                            </div>
                            <div class="form-group">
                                <input type="text" name="subject" placeholder="العنوان *" class="form-control" />
                            </div>
                            <div class="form-group">
                                <textarea type="text" name="message" placeholder="اكتب رسالتك *" rows="5" class="form-control message-control"></textarea>
                            </div>
                            <div class="form-group">
                                <button id="sendBtn" type="button" class="btn send-btn">ارسال</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="contact-form-information">
                        <h3 class="inner-heading">معلومات التواصل</h3>

                        <div class="infor">
                            <div>
                                <span><img src="{{ asset('website/images/loc.png') }}" class="img-responsive" /></span>
                            </div>
                            <div>
                                <p>وكالة مرئيات للدعاية والإعلان </p>

                                <p>  طريق الملك فهد</p> 
                                <p>   برج الفيصلية</p> 
                                <p>   الطابق 18</p> 
                                    
                                <p> ص.ب. 54995، الرياض، 11524، المملكة العربية السعودية</p>
                            </div>
                        </div>
                        <div class="infor">
                            <div>
                                <span><img src="{{ asset('website/images/call.png') }}" class="img-responsive" /></span>
                            </div>
                            <p class="ltr-column">
                                <a href="tel:+966114903929">+966-11-490-3929</a>
                            </p>
                        </div>
                        <div class="infor">
                            <div>
                                <span><img src="{{ asset('website/images/email.png') }}" class="img-responsive" /></span>
                            </div>
                            <p>
                                <a href="mailto:care@mustajarati.com" target="_blank" rel="noopener noreferrer">care@mustajarati.com</a>
                            </p>
                        </div>

                      
                        <div class="infor">
                        <div>
                                <span><img src="{{ asset('website/images/clock.png') }}" class="img-responsive" /></span>
                            </div>
                     <div>   <strong class="strongInfo">أوقات العمل : </strong><br> <p> أوقات العمل من يوم الأحد إلى الخميس, من التاسعة صباحاً حتى السادسة مساءً </p></div>

</div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#contactFrm [name="dial_code"]').select2({
            templateSelection: val => val.id ? `+${val.id}` : val.text,
        });

        $(document).on('click', '#sendBtn', function (e) {
            e.preventDefault();
            let $btn = $(this),
                $loader = $('.message-box');

            $.ajax({
                dataType: 'json',
                type: 'POST',
                url: "{{ route('website.contact_us') }}",
                data: $('#contactFrm').serialize(),
                beforeSend: () => {
                    $btn.attr('disabled', true);
                    $loader.html(`{!! transLang('loader_message') !!}`).removeClass('hide alert-danger alert-success').addClass('alert-info');
                },
                error: (jqXHR, exception) => {
                    $btn.attr('disabled', false);
                    $loader.html(formatErrorMessage(jqXHR, exception)).removeClass('hide alert-info').addClass('alert-danger');
                },
                success: response => {
                    $btn.attr('disabled', false);
                    $('#contactFrm')[0].reset();
                    $loader.html(response.message).removeClass('hide alert-danger alert-info').addClass('alert-success');
                },
            });
        });
    </script>
@endsection