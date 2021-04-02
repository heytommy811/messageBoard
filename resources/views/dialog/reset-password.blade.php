@section('reset-password')
<!-- パスワード変更のテンプレート -->
<div class="template reset_password">
    <div class="content reset_password sw-420">
        <div class="reset-description">
            <p>登録したメールアドレスを入力してください。</p>
            <p>パスワード再設定についてのご案内メールを送信します。</p>
        </div>
        <div class="password">
            <input type="email" class="mail" placeholder="メールアドレス" />
        </div>
        <div class="button-box"><button class="button bt1 reaction send">送信</button></div>
    </div>
</div>
@endsection