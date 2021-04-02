/* home.js */
$(function () {

    // 最初の変更
    if ($('[name="invite_id"]').val()) {
        getRequest('share/' + $('[name="invite_id"]').val());
        return;
    }
    // 初期処理
    initialize();
    // 検索のセットアップ
    setUpSearch();
    // 伝言板作成のセットアップ
    setUpCreateBoard();
    // プロフィール変更のセットアップ
    setUpChangeProfile();
    // パスワード変更のセットアップ
    setUpChangePassword();
    // 伝言板のセットアップ
    setUpBoard();
    // 伝言メッセージのセットアップ
    setUpMessage();

    // ホーム,ロゴが押された場合
    $('header .menu li.home,header .logo').click(function () {
        showHome(false);
    });
});
