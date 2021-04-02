@section('change-password')
<!-- パスワード変更のテンプレート -->
<div class="template change_password">
    <div class="content change_password">
        <ul>
            <li>
                <div class="label">変更前パスワード</div>
                <div class="dialo-input-text"><input type="password" class="password_before" placeholder="現在のパスワードを入力してください" /></div>
            </li>
            <li>
                <div class="label">変更後パスワード</div>
                <div class="dialo-input-text"><input type="password" class="password_after1" placeholder="新しいパスワードを入力してください" /></div>
            </li>
            <li>
                <div class="label"></div>
                <div class="dialo-input-text"><input type="password" class="password_after2" placeholder="確認用にもう一度入力してください" /></div>
            </li>
        </ul>
        <div class="error-message"></div>
        <div class="button-box"><button class="button bt1 reaction change">変更</button></div>
    </div>
</div>
@endsection