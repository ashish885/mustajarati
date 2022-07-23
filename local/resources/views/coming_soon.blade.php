<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <link rel="icon" type="image/x-icon" href="{{ URL::to('logo/favicon.ico') }}" />
        <title>Mustajarati</title>
        
        <link rel="stylesheet" href="{{ URL::to('website/coming-soon/compiled/flipclock.css') }}" />
        <link rel="stylesheet" href="{{ URL::to('website/coming-soon/css/coming-soon.css') }}" />

        <script src="{{ URL::to('website/js/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ URL::to('website/coming-soon/compiled/moment.js') }}"></script>
        <script src="{{ URL::to('website/coming-soon/compiled/flipclock.js') }}"></script>
    </head>

    <body>
        <div class="main-box">
            <div class="container">
                <div class="row">
                    <div class="logo-box">
                        <a href="{{ route('website.home') }}"><img src="{{ URL::to('website/coming-soon/images/logo.png') }}" style="width: 125px;" /></a>
                    </div>
                    <div class="content-box">
                        <h1 class="heading">Welcome!</h1>
                        <p class="info">We are happy to see you, our site is almost ready.</p>
                        <p class="gray-color info">Please come back later and we'll surprise you!</p>
                        <div class="development-box">
                            <h1 class="middle-section">Coming Soon & Maintenance Mode</h1>
                        </div>
                        <p class="font-24">Time Left Until <span class="red-text">Launching...</span></p>
                        <div class="clock" style="margin: 2em auto;"></div>
                        <div class="message"></div>
                    </div>
                </div>
            </div>

            <footer class="footer-box">
                <div class="container">
                    <div class="row">
                        <div class="cok-md-7">
                            <p class="copy-right">© 2021 - Mustajarati. All Right Reserved.</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <script type="text/javascript">
            var clock;
            $(document).ready(function () {
                var deadline_date = moment("2021-08-11");
                deadline_date = deadline_date.diff(moment(), "second");
                var clock = $(".clock").FlipClock(3600 * 24 * 3, {
                    clockFace: "DailyCounter",
                    countdown: true,
                });
                clock.setTime(deadline_date);
                clock.setCountdown(true);
                clock.start();
            });
        </script>
    </body>
</html>
