@extends('layout.common')
 
@section('title', 'ホーム')
@section('keywords', '伝言板')
@section('description', '伝言板ホーム')
@section('pageCss')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/lib/jquery-ui.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/lib/perfect-scrollbar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/lib/cropper.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/dialog.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/board.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/message.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/home.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/search.css') }}">
@endsection
 
@include('layout.header', ['menu' => true, 'account' => true])
 
@section('content')
    <div class="content home">
        <div class="content-inner">
            @if (session('message'))
                <div class="error-message show">
                    {{ session('message') }}
                </div>
            @endif
            <div class="massage-board-list"></div>
        </div>
    </div>
    <div class="content board">
        <div class="content-inner">
            @if (session('message'))
                <div class="error-message show">
                    {{ session('message') }}
                </div>
            @endif
            <div class="board-top"></div>
        </div>
    </div>
    @include('page.search')
    @yield('search')

        
    @include('dialog.create-board')
    @include('dialog.change-profile')
    @include('dialog.change-password')
    @include('dialog.board-menu-member')
    @include('dialog.board-menu-message')
    @include('dialog.board-menu-share')
    @include('dialog.board-menu-manage')
    @include('dialog.message-modify')
    @include('dialog.comment')
    <div class="dialog-template">
        @yield('create-board')
        @yield('change-profile')
        @yield('change-password')
        @yield('board-menu-member')
        @yield('board-menu-message')
        @yield('board-menu-share')
        @yield('board-menu-manage')
        @yield('message-modify')
        @yield('comment')
    </div>

{{ csrf_field() }}
@endsection
@if (isset($invite_id))
    <input type="hidden" name="invite_id" value="{{$invite_id}}" />
@endif

@section('pageJs')
    <script src="{{ asset('public/js/lib/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('public/js/lib/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('public/js/lib/cropper.min.js') }}"></script>
    <script src="{{ asset('public/js/home.js') }}"></script>
    <script src="{{ asset('public/js/search.js') }}"></script>
    <script src="{{ asset('public/js/createBoard.js') }}"></script>
    <script src="{{ asset('public/js/changeProfile.js') }}"></script>
    <script src="{{ asset('public/js/changePassword.js') }}"></script>
    <script src="{{ asset('public/js/board.js') }}"></script>
    <script src="{{ asset('public/js/message.js') }}"></script>
@endsection