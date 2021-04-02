@section('board-menu-manage')
<!-- 伝言板メニュー[管理]テンプレート -->
<div class="template manage">
    <div class="content board_manage">
        <ul class="flex-box">
            <li>
                <div class="label">参加方式</div>
                <div class="dialo-input-text">
                    <div class="table radio-group">
                        <label class="slide-checkbox">
                            作成者が認証する
                            <input type="checkbox" class="join_approval">
                            <span></span>
                        </label>
                    </div>
                </div>
            </li>
            <li>
                <div class="label"></div>
                <div class="dialo-input-text">
                    <label class="slide-checkbox">
                        パスワードで認証する
                        <input type="checkbox" class="join_password">
                        <span></span>
                    </label>
                </div>
            </li>
            <li>
                <div class="label"></div>
                <div class="dialo-input-text">
                    <input type="text" autoComplete="off" name="password" class="password" placeholder="パスワードを入力してください" value="" />
                </div>
            </li>
            <li>
                <div class="label">検索可否</div>
                <div class="dialo-input-text">
                    <label class="slide-checkbox">
                        検索で表示する
                        <input type="checkbox" class="search_possible" checked="checked">
                        <span></span>
                    </label>
                </div>
            </li>
            <li>
                <div class="label">参加者の権限</div>
                <div class="dialo-input-text">
                    <label class="slide-checkbox">
                        伝言の投稿
                        <input type="checkbox" class="auth_post">
                        <span></span>
                    </label>
                </div>
            </li>
            <li>
                <div class="label"></div>
                <div class="dialo-input-text">
                    <label class="slide-checkbox">
                        伝言板をURLで共有
                        <input type="checkbox" class="auth_share">
                        <span></span>
                    </label>
                </div>
            </li>
        </ul>
        <div class="button-box"><button class="button bt1 reaction button_modify">変更</button></div>
    </div>
</div>
@endsection