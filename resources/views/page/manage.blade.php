@extends('layout.common')
 
@section('title', '管理者')
@section('keywords', '管理者')
@section('description', '管理者')
@section('pageCss')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/manage.css') }}">
@endsection
 
@include('layout.header', ['menu' => false, 'account' => false])
 
@section('content')
    <div class="content">
        {{ csrf_field() }}
        <a href="logout">ログアウト</a>
        <div class="system-settings">
            <h2>システム設定一覧</h2>
            <ul>
                <li class="top"><div class="id">id</div><div class="key">key</div><div class="value">value</div><div class="update">更新</div></li>
                @foreach ($system_settings as $setting)
                    <li><div class="id">{{$setting['id']}}</div><div class="key">{{$setting['key']}}</div><div class="value"><input type="text" value="{{$setting['value']}}" /></div><div class="update"><button>更新</button></div></li>
                @endforeach
            </ul>
        </div>
        <div class="account-request">
            <h2>アカウント申込一覧</h2>
            <ul>
                <li class="top"><div class="name">名前</div><div class="mail">メールアドレス</div><div class="date">有効期限</div><div class="update">本登録</div></li>
                @foreach ($account_request as $request)
                    <li><div class="name">{{$request['name']}}</div><div class="mail">{{$request['mail']}}</div><div class="date">{{$request['date']}}</div><input type="hidden" name="id" value="{{$request['id']}}" /><div class="update"><button>本登録</button></div></li>
                @endforeach
            </ul>
        </div>
        <div class="user">
            <h2>ユーザー検索</h2>
            <div class="search-keyword"><div class="label">id or mail or name:</div><input type="text" name="keyword" /><button>検索</button></div>
            <div class="error-message">検索結果が存在しません。</div>
            <table class="user-search-result">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>mail</th>
                        <th>name</th>
                        <th>最終ログイン</th>
                        <th>作成日</th>
                        <th>失敗回数</th>
                        <th>失敗日時</th>
                        <th>ロック回数</th>
                        <th>ロック日時</th>
                        <th>凍結</th>
                        <th>ロック解除</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('pageJs')
    <script src="{{ asset('public/js/manage.js') }}"></script>
@endsection