@extends('layout.common')
 
@section('title', 'パスワードリセット')
@section('keywords', '伝言板')
@section('description', '伝言板 パスワードリセット')
@section('pageCss')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/lib/cropper.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/dialog.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/resetPassword.css') }}">
@endsection
 
@include('layout.header', ['menu' => false, 'account' => false])
 
@section('content')
    <div class="content reset-password">
        <div class="content-inner">
            {{ csrf_field() }}

            <input type="hidden" name="cert_id" value="{{$cert_id}}" /> 

            <div class="password-form">
                <ul class="flex-box">
                    <li>
                        <div class="label">新しいパスワード</div>
                        <div class="password"><input type="password" class="new_password" placeholder="新しいパスワードを入力してください" /></div>
                    </li>
                    <li>
                        <div class="label">確認用</div>
                        <div class="password"><input type="password" class="new_password_confirm" placeholder="新しいパスワードを入力してください" /></div>
                    </li>
                </ul>
                <div class="button-box"><button class="button bt1 reaction reset">リセット</button></div>
            </div>
            <div class="login-box"><button class="button bt1 reaction login">ログインへ</button></div>
        </div>
    </div>
@endsection



@section('pageJs')
    <script src="{{ asset('public/js/resetPassword.js') }}"></script>
@endsection