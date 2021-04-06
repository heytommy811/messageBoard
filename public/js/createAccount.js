/**
 * アカウント作成初期処理
 */
function setUpCreateAccount() {

    // アカウント作成ボタン押下時のイベント
    $('#button_account').on('click', function() {
        showCreateAccount();
        return false;
    });

    // 入力項目にvalidクラスを付与するイベント
    $('.dialog').on('input', '[name=acount_input]', function() {
		if ($(this).val()) {
			if (!$(this).hasClass('valid')) {
				// 入力済みの場合はvalidをクラスに追加する
				$(this).addClass('valid');
			}
		} else {
			$(this).removeClass('valid');
		}
    });
    // アイコン選択用のダイアログ(cropper用)
    $('.cropper-dialog .close').click(function () {
        $('.cropper-dialog').fadeOut(200);
        // ファイル選択をリセット
        $('.create_account input[type=file]').val('');
    });

    // ファイル選択ボタンクリックイベント
    setFileSelectEvent();

    // アイコン画像選択のOKボタン押下時のイベント
    $('.cropper-dialog button.ok').click(function () {
        let cropper_data = $('#cropper_image').cropper('getData');
        icon.w = Math.round(cropper_data.width);
        icon.h = Math.round(cropper_data.height);
        icon.x = Math.round(cropper_data.x);
        icon.y = Math.round(cropper_data.y);
        icon.src = $('.icon-preview img').attr('src');
        console.log(icon);
        setCanvasImage(icon);
        icon.file = $('.dialog input[type=file]')[0].files[0];
        $('.cropper-dialog .close').click();
    });

    // 保存ボタン押下時のイベント
    $('.dialog').on('click', 'button.create', function () {

        if (!validate()) {
            return;
        }

        showConfirm('入力の内容でアカウントを作成します。</br>よろしいですか？', function() {
            let form = new FormData();
            form.append('account_name', $('.dialog .account_name').val());
            form.append('mail', $('.dialog .account_mail').val());
            form.append('password', $('.dialog .account_password').val());
    
            // アイコンサイズ編集用のパラメータ
            if (icon.file) {
                form.append('file', icon.file);
                form.append('width', Math.round(icon.w));
                form.append('height', Math.round(icon.h));
                form.append('x', Math.round(icon.x));
                form.append('y', Math.round(icon.y));
            }
    
            startBlockLoading();
            getResponse({
                url: 'account',
                type: 'POST',
                dataType: 'json',
                data: form,
                processData: false,
                contentType: false,
            }, null, false, 'POST').done(function (response) {
                stopBlockLoading();
                $('.dialog').fadeOut(200);
                showAlert('確認メールに記載されたURLを開いて登録を完了してください。', function () {
                    redirectAppRoot();
                });
                
            }).fail(function (response) {
                console.error(response);
                stopBlockLoading();
            });
        });
    });

    /**
     * アカウント作成の入力値チェック
     */
    function validate() {
        let result = true;
        if (!$('.dialog input.account_name').val()) {
            setTextError($('.dialog .account_name'), "入力してください");
            result = false;
        }
        if (!$('.dialog input.account_mail').val()) {
            setTextError($('.dialog .account_mail'), "入力してください");
            result = false;
        }
        if (!$('.dialog input.account_password').val()) {
            setTextError($('.dialog .account_password'), "入力してください");
            result = false;
        } else {
            if ($('.dialog input.account_password_confirm').val()) {
                if ($('.dialog input.account_password').val() !== $('.dialog input.account_password_confirm').val()) {
                    setTextError($('.dialog .account_password_confirm'), "パスワードが一致しません");
                    result = false;
                }
            }
        }
        if (!$('.dialog input.account_password_confirm').val()) {
            setTextError($('.dialog .account_password_confirm'), "入力してください");
            result = false;
        }

        return result;
    }
}

/**
 * アカウント作成表示処理
 */
function showCreateAccount() {
    let template = $('.template.create_account').clone();
    showCommonDialog('アカウントを作成する', template.children());
    icon = {};
}