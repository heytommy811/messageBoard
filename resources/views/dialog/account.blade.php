@section('account')
<!-- アカウント作成テンプレート -->
<div class="template create_account">
    <div class="content create_account">
        <ul>
            <li>
                <input type="text" name="acount_input" autocapitalize="off" autoComplete="off" class="account_name" />
                <span class="input-label-wrapper"><label>アカウント名</label></span>
            </li>
            <li>
                <input type="email" name="acount_input" autocapitalize="off" autoComplete="off" class="account_mail" />
                <span class="input-label-wrapper"><label>メールアドレス</label></span>
            </li>
            <li>
                <input type="password" name="acount_input" autocapitalize="off" autoComplete="off" class="account_password" />
                <span class="input-label-wrapper"><label>パスワード</label></span>
            </li>
            <li>
                <input type="password" name="acount_input" autocapitalize="off" autoComplete="off" class="account_password_confirm" />
                <span class="input-label-wrapper"><label>パスワード(確認用)</label></span>
            </li>
            <li>
                <input type="file" style="display:none;" />
                <div class="icon">
                    <span class="icon-select-button"></span>
                    <img src="{{asset('public/img/default_user_icon.png')}}" />
                    <canvas></canvas>
                </div>
            </li>
        </ul>
        <div class="button-box"><button class="button bt1 reaction create">作成する</button></div>
    </div>
</div>
@endsection