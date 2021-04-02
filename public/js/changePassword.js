function showChangePassword() {
    let template = $('.template.change_password').clone();
    showCommonDialog('パスワード変更', template.children());
}

/**
 * パスワード変更のセットアップ
 */
function setUpChangePassword() {

    // 保存ボタン押下時のイベント
    $('.dialog').on('click', 'button.change', function () {

        let inputArray = [
            $('.dialog .password_before'),
            $('.dialog .password_after1'),
            $('.dialog .password_after2'),
        ];
        let validate = true;
        for (let i = 0; i < inputArray.length; i++) {
            if (!inputArray[i].val()) {
                // 必須チェック
                setTextError(inputArray[i], "入力してください");
                validate = false;
            }
        }
        if (!validate) return;
        if (inputArray[1].val() !== inputArray[2].val()) {
            $('.dialog .error-message').text('確認用のパスワードが一致しません。');
            $('.dialog .error-message').slideDown(300);
            return;
        }

        startLoading();
        getResponse('account/password', {
            password_before: inputArray[0].val(),
            password_after: inputArray[1].val(),
        }, false, 'PUT').done(function (response) {
            $('.dialog .error-message').empty();
            if (0 < response.validateError.length) {
                // エラーメッセージを表示する
                $('.dialog .error-message')[0].innerHTML = response.validateError.join("<br>");
                $('.dialog .error-message').slideDown(300);
            } else {
                // 正常終了
                $('.dialog').fadeOut(200);
            }
        }).always(function () {
            stopLoading();
        });
        return false;
    });
}
