@extends('layout.common')
 
@section('title', 'エラー')
@section('keywords', '伝言板')
@section('description', '伝言板エラー')
@section('pageCss')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/error.css') }}">
@endsection
 
@include('layout.header', ['menu' => false, 'account' => false])
 
@section('content')
    <div class="content">
        <div class="content-inner">
            @if (session('message'))
                <div class="error-message">
                    {{ session('message') }}
                </div>
            @endif
            <div class="error">お探しのページは見つかりませんでした。</div>
            <div class="error_description">お手数をおかけしますが、もう一度お試しください。</div>
            <div class="buttons">
                <div class="button_center"><button id="to_login" class="button bt1 reaction" onclick="getRequest('')">ログインへ</button></div>
            </div>
        </div>
    </div>
@endsection
