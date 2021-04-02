@section('header')
<div class="header-inner" id="header-inner">
    <div class="logo">MessageBoard
        <div class="loading-wrapper"><ul class="content-loading"><li></li><li></li></ul></div>
    </div>
    @if ($menu)
    <ul class="menu">
        <li class="home active" title="ホームへ">ホーム</li>
        <li class="search" title="伝言板を検索する">検索</li>
    </ul>
    @endif
@if ($account)
    <div class="account">
        <img class="account-icon header-icon" src="{{$account_icon}}"  title="プロフィールと設定" />
        <div class="account-menu">
            <ul>
                <li>
                    <div class="account-name">{{$account_name}}</div>
                    <div class="account-mail">{{$account_mail}}</div>
                </li>
                <li class="horizon-border"></li>
                <li class="menu create" title="新しい伝言板を作成する">伝言板を作成</li>
                <li class="horizon-border"></li>
                <li class="menu change_profile">プロフィール編集</li>
                <li class="menu change_password">パスワード変更</li>
                <li class="horizon-border"></li>
                <li class="menu" onClick="getRequest('logout')">ログアウト</li>
            </ul>
        </div>
    </div>
@endif
</div>
@endsection