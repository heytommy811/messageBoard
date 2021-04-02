@section('board-menu-member')
<!-- 伝言板メニュー[メンバー一覧]テンプレート -->
<div class="template member">
    <div class="content board_menu_member sw-400">
        <div class="member-list">
            <h2 class="title icon join-member-icon">参加者</h2>
            <ul></ul>
        </div>
        <div class="member-request">
            <h2 class="title icon request-member-icon">申請中</h2>
            <ul></ul>
        </div>
        <div class="member-modify-dialog">
            <div class="dialog-contents">
                <div class="title">権限変更</div>
                <div class="close"></div>
                <div class="dialog-contents-inner">
                    <div class="content modify_member">
                        <ul>
                            <li>
                                <div class="dialo-input-text">
                                    <label class="slide-checkbox">
                                        伝言の投稿
                                        <input type="checkbox" class="auth_post" checked="checked">
                                        <span></span>
                                    </label>
                                </div>
                            </li>
                            <li>
                                <div class="dialo-input-text">
                                    <label class="slide-checkbox">
                                        伝言板をURLで共有
                                        <input type="checkbox" class="auth_share">
                                        <span></span>
                                    </label>
                                </div>
                            </li>
                        </ul>
                        <div class="button-box"><button class="button bt1 reaction modify">変更</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection