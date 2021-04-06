/**
 * パスワードリセット初期処理
 */
function setUpResetPassword() {
    
	$('.password-reset').on('click', function() {
        let template = $('.template.reset_password').clone();
        showCommonDialog('パスワードをリセットする', template.children());
    });
    
    // 送信ボタン押下イベント
    $('.dialog').on('click', '.reset_password button.send', function() {

        const mail = $('.dialog .reset_password .mail').val();
        if (!mail) {
            setTextError($('.dialog .reset_password .mail'), "入力してください");
            return;
        }

        // パスワードリセットの案内メール送信処理
        startBlockLoading();
        getResponse('account/password/reset/send', { mail: mail}, false, 'POST').done(function (response) {
            showAlert("パスワードリセットのご案内メールを送信しました。");
            $('.dialog').fadeOut(200);
        }).always(function() {
            stopBlockLoading();
        });
    });
}

$(function() {

    // リセットボタン押下
    $('button.reset').click(function() {
        if (!validate()) {
            return;
        }

        const data = {
            cert_id: $('[name=cert_id]').val(),
            password: $('.new_password').val(),
        };

        console.log("パスワードリセット：", data);

        startBlockLoading();
        getResponse('account/password/reset', data, false, 'POST').done(function (response) {
            showAlert('パスワードをリセットしました。');
            $('.password-form').hide();
            $('.login-box').show();
        }).always(function() {
            stopBlockLoading();
        });
    });
    // ログインへボタン押下
    $('button.login').click(function() {
        redirectAppRoot();
    });


    /**
     * パスワードを検証する
     */
    function validate() {
        let validate = true;

        if (!$('.new_password').val()) {
            // 必須チェック
            setTextError($('.new_password'), "入力してください");
            validate = false;
        }
        if (!$('.new_password_confirm').val()) {
            // 必須チェック
            setTextError($('.new_password_confirm'), "入力してください");
            validate = false;
        }
        if (validate && $('.new_password').val() !== $('.new_password_confirm').val()) {
            showAlert('確認用のパスワードが一致しません。');
            return;
        }
        return validate;
    }
});