<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" itemprop="description" content="@yield('description')">
    <meta name="keywords" itemprop="keywords" content="@yield('keywords')">
    <link rel="shortcut icon" href="{{ asset('public/favicon.ico') }}">
    <title>MessageBoard - @yield('title')</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=M+PLUS+1p&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/lib/loaders.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/style.css') }}">
@yield('pageCss')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/responsive.css') }}">

</head>
<body>
    <div class="loading">
        <div class="loader">
            <div class="loader-inner ball-spin-fade-loader">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <input type="hidden" name="base_url" value="{{ url('')}}" />
    <header class="header">
        @yield('header')
    </header>
    <div class="main-contents">
        @yield('content')
    </div>
    <div class="dialog">
        <div class="dialog-contents">
            <div class="title"></div>
            <span class="close"></span>
            <div class="dialog-contents-inner"></div>
        </div>
    </div>
    <div class="alert-dialog" id="alert_dialog">
        <div class="dialog-contents">
            <div class="alert-message"></div>
            <div class="alert-button">OK</div>
        </div>
    </div>
    <div class="confirm-dialog" id="confirm_dialog">
        <div class="dialog-contents">
            <div class="confirm-message"></div>
            <div class="confirm-button">
                <div>はい</div>
                <div>いいえ</div>
            </div>
        </div>
    </div>
    <div class="cropper-dialog">
        <div class="dialog-contents">
            <div class="title">写真を選択</div>
            <div class="close"></div>
            <div class="dialog-contents-inner">
                <div class="icon-preview"></div>
                <div class="button-box"><button class="button bt1 reaction ok">OK</button></div>
            </div>
        </div>
    </div>
    <script src="{{ asset('public/js/lib/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('public/js/common.js') }}"></script>
    @yield('pageJs')
</body>
</html>