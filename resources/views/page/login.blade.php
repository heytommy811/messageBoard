@extends('layout.common')
 
@section('title', 'ログイン')
@section('keywords', '伝言板')
@section('description', '伝言板ログイン')
@section('pageCss')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/lib/cropper.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/dialog.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/login.css') }}">
@endsection
 
@include('layout.header', ['menu' => false, 'account' => false])
 
@section('content')
    <div class="content login">
        <div class="content-inner">

            <div class="login-form">
                <form action="{{asset('login')}}" method='POST'>
                    <!-- {{ csrf_field() }} -->
                    @if (session('message'))
                        <div class="error-message">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="error-message">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <ul>
                        <li>
                            <input type="email" autocapitalize="off" autoComplete="off" name="mail" class="text-mail" />
                            <span class="input-label-wrapper"><label>メールアドレス</label></span>
                        </li>
                        <li>
                            <input type="password" name="password" class="text-password" />
                            <span class="input-label-wrapper"><label>パスワード</label></span>
                        </li>
                        <li>
                            <button id="button_login" class="button bt1 reaction">ログイン</button>
                        </li>
                    </ul>
                </form>
                <button id="button_account" class="button bt2 reaction">アカウント作成</button>
                <div class="password-reset link">パスワードを忘れた方はこちら</div>
            </div>
        </div>
    </div>

    @include('dialog.account')
    @include('dialog.reset-password')
    <div class="dialog-template">
        @yield('account')
        @yield('reset-password')
    </div>
@endsection



@section('pageJs')
    <script src="{{ asset('public/js/lib/cropper.min.js') }}"></script>
    <script src="{{ asset('public/js/login.js') }}"></script>
    <script src="{{ asset('public/js/createAccount.js') }}"></script>
    <script src="{{ asset('public/js/resetPassword.js') }}"></script>
@endsection