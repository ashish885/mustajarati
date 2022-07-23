<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mustajarati</title>
</head>
<body>
    <h2>Please wait..</h2>
    <script type="text/javascript">
        function redirectToDownloadApp() {
            var userAgent = window.navigator.userAgent,
                platform = window.navigator.platform,
                macosPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
                windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
                iosPlatforms = ['iPhone', 'iPad', 'iPod'],
                os = null;

            if (macosPlatforms.indexOf(platform) !== -1 || iosPlatforms.indexOf(platform) !== -1) {
                location.replace('{!! $apple_store_url !!}');
            } else {
                location.replace('{!! $play_store_url !!}');
            }

            return os;
        }
        redirectToDownloadApp();
    </script>
</body>
</html>