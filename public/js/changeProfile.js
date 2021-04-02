function showChangeProfile() {
    let template = $('.template.change_profile').clone();
    showCommonDialog('プロフィール', template.children());
    icon = {};
}
/**
 * プロフィール変更の初期処理
 */
function setUpChangeProfile() {

    var icon = {};

    // アイコン選択用のダイアログ(cropper用)
    $('.cropper-dialog .close').click(function () {
        $('.cropper-dialog').fadeOut(200);
        // ファイル選択をリセット
        $('.change_profile input[type=file]').val('');
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
    $('.dialog').on('click', 'button.save', function () {

        let accountName = $('.dialog .account_name').val();
        if (!accountName) {
            setTextError($('.dialog .account_name'), "入力してください");
            return false;
        }

        let form = new FormData();
        form.append('setting_account_name', accountName);

        // アイコンサイズ編集用のパラメータ
        if (icon.file) {
            form.append('file', icon.file);
            form.append('width', Math.round(icon.w));
            form.append('height', Math.round(icon.h));
            form.append('x', Math.round(icon.x));
            form.append('y', Math.round(icon.y));
            form.append('_method', 'PUT');
        }

        startLoading();
        $('.dialog').fadeOut(200);
        getResponse({
            url: 'account/profile',
            type: 'POST',
            dataType: 'json',
            data: form,
            processData: false,
            contentType: false,
        }, null, false, 'PUT').done(function (response) {
            // アイコン画像を差し替える
            $('header .header-icon').attr('src', response.src);
            $('.template.change_profile .icon > img').attr('src', response.src);
            $('header .account-name').text(accountName);
            $('.template.change_profile .account_name').val(accountName);
        }).always(function () {
            stopLoading();
        });
        return false;
    });
}
