function showCreateBoard() {
    let template = $('.template.create').clone();
    showCommonDialog('新しい伝言板を作る', template.children());
    // パスワードの行を非表示にする
    $('.dialog .create_board ul.flex-box li:nth-child(6)').hide();
}

/**
 * 伝言作成ダイアログの初期処理
 */
function setUpCreateBoard() {

    // 作成ボタン押下時のイベント
    $('.dialog').on('click', 'button.create', function () {
        let result = true;

        if (!$('.dialog .create_board input.board_name').val()) {
            setTextError($('.dialog .create_board input.board_name'), "入力してください");
            result = false;
        }
        if (!$('.dialog .create_board input.creater').val()) {
            setTextError($('.dialog .create_board input.creater'), "入力してください");
            result = false;
        }
        if ($('.dialog .create_board input.join_password').prop('checked') && !$('.dialog .create_board input.password').val()) {
            setTextError($('.dialog .create_board input.password'), "入力してください");
            result = false;
        }

        if (!result) return false;

        const joinType = getJoinType(
            $('.dialog .create_board input.join_approval').prop('checked'),
            $('.dialog .create_board input.join_password').prop('checked')
        );
        const defaultAuthId = getAuthID(
            $('.dialog .create_board input.auth_post').prop('checked'),
            $('.dialog .create_board input.auth_share').prop('checked')
        );
        let data = {
            title: $('.dialog .create_board input.board_name').val(),
            manage_user_name: $('.dialog .create_board input.creater').val(),
            join_type: joinType,
            join_password: $('.dialog .create_board input.password').val(),
            search_type: $('.dialog .create_board input.search_possible').prop('checked') ? 1 : 0,
            default_auth_id: defaultAuthId,
        }

        console.log("新規伝言版作成：", data);
        
        // 先に画面を閉じてローディングを開始する
        $('.dialog').fadeOut(200);
        startLoading();
        getResponse('board', data, false, 'POST').done(function (response) {
            refreshTop();
        }).fail(function() {
            stopLoading();
        });
    });

    var input_manage_member_name = "";
    $('.dialog').on('change', 'input[type="checkbox"]', function (e) {
        if ($(this).hasClass('use_account')) {

            // ユーザー名を使用するチェックボックスの切替イベント
            const creater = $(this).parents('ul').find('input.creater');
            if (e.target.checked) {
                input_manage_member_name = $('#creater').val();
                creater.val($('header .account-name').text());
                creater.prop('disabled', true);
                creater.removeClass('text-error');
                creater.parent().removeClass('text-error');
            } else {
                creater.val(input_manage_member_name);
                creater.prop('disabled', false);
            }
        } else if ($(this).hasClass('join_password')) {

            // パスワードで認証するのチェックボックスの切替イベント
            if (e.target.checked) {
                // パスワードを表示する
                $(this).parents('li').next().show(200);

            } else {
                $(this).parents('li').next().hide(200);

            }
        }
    });
}
