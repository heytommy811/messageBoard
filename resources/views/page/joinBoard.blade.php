<div class="join-board-page">
    <p class="board-title">{{$title}}へ参加します。</p>
    <p class="board-title">まずはこの伝言板で使用するあなたの名前を登録してください。</p>
    <ul class="flex-box">
        <li>
            <div class="label">名前</div>
            <div class="page-input-text">
                <input type="text" autoComplete="off" name="manage_user_name" class="new-board-input-text member_name" placeholder="この伝言板で使用するユーザー名を入力してください" value="{{$name}}" />
            </div>
        </li>
        <li>
            <div class="label"></div>
            <div class="page-input-text">
                <label class="slide-checkbox">
                    アカウント名を使用する
                    <input type="checkbox" class="use_account" checked="">
                    <span></span>
                </label>
            </div>
        </li>
    </ul>
    <div class="button-box"><button class="button bt1 reaction join">登録</button></div>
</div>