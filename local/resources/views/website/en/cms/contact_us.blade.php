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
                        <h3 class="inner-heading mb-5">Drop us a line</h3>
                        <div class="message-box alert hide"></div>
                        <form id="contactFrm">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="name" placeholder="Enter your name *" class="form-control" />
                            </div>
                            <div class="form-group">
                                <input type="text" name="email" placeholder="Enter your email address *" class="form-control" />
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
                                <input type="number" name="mobile_no" placeholder="Enter your mobile number *" class="form-control" />
                            </div>
                            <div class="form-group">
                                <input type="text" name="subject" placeholder="Subject *" class="form-control" />
                            </div>
                            <div class="form-group">
                                <textarea type="text" name="message" placeholder="Enter your message *" rows="5" class="form-control message-control"></textarea>
                            </div>
                            <div class="form-group">
                                <button id="sendBtn" type="button" class="btn send-btn">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="contact-form-information">
                        <h3 class="inner-heading">Contact Information</h3>

                        <div class="infor">
                            <div>
                                <span><img src="{{ asset('website/images/loc.png') }}" class="img-responsive" /></span>
                            </div>
                            <div>
                                <p>Mareyat Advertising Agency</p>

                                <p>King Fahd Road</p>
                                <p>Al Faisaliah tower</p>
                                <p>Floor 18</p>

                                <p>P O Box. 54995, Riyadh, 11524, Kingdom of Saudi Arabia</p>
                            </div>
                        </div>
                        <div class="infor">
                            <div>
                                <span><img src="{{ asset('website/images/call.png') }}" class="img-responsive" /></span>
                            </div>
                            <p>
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
                     <p>   <strong class="strongInfo"> working hours : </strong> <br> our working hours from Sunday to Thursday, From 9:00 A.M until 6:00 P.M</p>

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