/**
 * 伝言ページを開く
 */
function openBoardPage() {
    $('header .menu li').removeClass('active');
    $('.massage-board-list').hide(100);
    $('.content.board').show();
    $('.content.board').animate({ top: 0 }, 100);
}

/**
 * 伝言板を表示する
 * @param {*} dgb_id 
 */
function showBoard(dgb_id, skipHistory) {

    // 伝言ページを開く
    openBoardPage();

    if (!skipHistory) {
        // 履歴を追加する
        addHistory('?page=board&d='+dgb_id);
    }
    $('.content.home .error-message.show').removeClass('show');

    if ($('.board-top').find('.loading-wrapper').length === 0) {
        $('.board-top').empty();
        startLoading($('.board-top'));
    }
    getResponse('board', { dgb_id: dgb_id }, true, 'GET').then(function (response) {
        if (response.status) {
            if (response.status == 'timeout') {
                // ログインへリダイレクトする
                showAlert(response.message, function() {
                    redirectAppRoot()
                });
            } else if (response.status == 'error' && response.message) {
                // エラーメッセージを表示する
                showAlert(response.message, function() {
                    showHome();
                });
            }
        } else {
            $('.board-top').append(response);
            $('.board-top').data('dgb_id', dgb_id);

        }
    }).always(function () {
        stopLoading($('.board-top'));
    });
}

function setUpBoard() {
    // トップページで伝言板を押下
    $('.massage-board-list').on('click', 'ul.board li', function (e) {
        let dgb_id = $(this).find('[name=dgb_id]').val();
        showBoard(dgb_id, false);
    });

    // 伝言板メニューボタン押下
    $('.board-top').on('click', '.board-menu-buttons li', function (e) {
        const dgb_id = $('.board-top').data('dgb_id');
        console.log("伝番版ID:", dgb_id);
        if ($(e.target).hasClass('member')) {
            // メンバーボタン押下時
            showMember(dgb_id);
        } else if ($(e.target).hasClass('create_message')) {
            // 伝言作成ボタン押下時
            showCreateMessage();
        } else if ($(e.target).hasClass('share')) {
            // 共有ボタン押下時
            showShare(dgb_id);
        } else if ($(e.target).hasClass('manage_board')) {
            // 管理ボタン押下時
            showManageBoard(dgb_id);
        }
    });

    // メッセージ押下時のイベント
    $('.board-top').on('click', '.message-list > ul > li', function (e) {
        let dgn_id = $(this).find('[name=dgn_id]').val();
        showMessage(dgn_id);
    });

    // 参加者ダイアログのイベントを設定する
    setUpMemberEvents();

    // 伝言板作成ダイアログのイベントを設定する
    setUpMessageEvents();

    // 共有ダイアログのイベントを設定する
    setUpShareEvents();

    // 管理ダイアログのイベントを設定する
    seUpManageEvents();

    // 伝言板参加ページのイベントを設定する
    setUpJoinBoardEvents();

    /**
     * 参加者一覧ダイアログのイベント
     */
    function setUpMemberEvents() {

        // 参加者一覧の変更ボタン押下時のイベント
        $('.dialog').on('click', '.board_menu_member .button_modify', function (e) {
            const user_id = $(e.target).parent().find('[name=user_id]').val();
            const auth_id = $(e.target).parent().find('[name=auth_id]').val();
            // 更新時に必要なので、ダイアログにユーザーIDを持たせる
            $('.dialog .member-modify-dialog').data('user_id', user_id);
            // アニメーションでチェックが動くのでクラスを削除
            $('.dialog .member-modify-dialog li label').removeClass('slide-checkbox');
            // 投稿権限をON
            $('.dialog .member-modify-dialog input.auth_post').prop('checked', (auth_id == 2 || auth_id == 4));
            // 共有権限をON
            $('.dialog .member-modify-dialog input.auth_share').prop('checked', (auth_id == 3 || auth_id == 4));
            // クラスを戻す
            $('.dialog .member-modify-dialog li label').addClass('slide-checkbox');
            $('.dialog .member-modify-dialog').fadeIn(200);
        });
        // 権限変更ダイアログの閉じるボタン押下時のイベント
        $('.dialog').on('click', '.member-modify-dialog .close', function (e) {
            $('.dialog .member-modify-dialog').fadeOut(200);
        });
        // 権限変更ダイアログの閉じるボタン押下時のイベント
        $('.dialog').on('click', '.member-modify-dialog .modify', function (e) {
            const auth_id = getAuthID(
                $('.dialog .member-modify-dialog input.auth_post').prop('checked'),
                $('.dialog .member-modify-dialog input.auth_share').prop('checked')
            );
            const update_user_id = $('.dialog .member-modify-dialog').data('user_id');
            // 権限更新処理を実行
            const data = {
                dgb_id: $('.board-top').data('dgb_id'),
                user_id: update_user_id,
                auth_id: auth_id,
            };
            console.log('権限変更：', data);
            getResponse('member', data, false, 'PUT').done(function (response) {

                // 変更したユーザーの権限を変えておく
                $('.dialog .board_menu_member .member-list li').each(function (i, ele) {
                    if ($(ele).find('[name=user_id]').val() == update_user_id) {
                        $(ele).find('[name=auth_id]').val(auth_id);
                        return;
                    }
                });
            });
            // 完了を待たずに閉じる
            $('.dialog .member-modify-dialog').fadeOut(200);
        });

        // 参加申請のボタンイベント
        $('.dialog').on('click', '.board_menu_member .answer-buttons button', function (e) {
            const join_request_id = $(e.target).parents('.answer-buttons').data('join_request_id');
            console.log("申請者ユーザーID：", join_request_id);
            console.log($(e.target).parents('.answer-buttons'));
            if ($(e.target).hasClass('request_ok')) {
                // 許可
                showConfirm('申請者の参加を許可します。</br>よろしいですか？', function () {
                    acceptMember(join_request_id, 1);
                });
            } else {
                // 拒否
                showConfirm('申請者の参加を拒否します。</br>よろしいですか？', function () {
                    acceptMember(join_request_id, 2);
                });
            }
        });
    }

    /**
     * メンバーの一覧をダイアログ表示する
     * @param {*} dgb_id 
     */
    function showMember(dgb_id) {

        let template = $('.template.member').clone();
        showCommonDialog('参加者一覧', template.children());
        refreshMember(dgb_id);

    }

    /**
     * 参加者ダイアログをリフレッシュする
     * @param {*} dgb_id 
     */
    function refreshMember(dgb_id) {

        $('.dialog .board_menu_member .member-list').hide();
        $('.dialog .board_menu_member .member-request').hide();

        $('.dialog .board_menu_member .member-list ul').empty();
        $('.dialog .board_menu_member .member-request ul').empty();

        startLoading($('.dialog .dialog-contents-inner'));
        getResponse('member', { dgb_id: dgb_id }, false, 'GET').then(function (response) {
            console.log("参加者情報取得：", response);

            // 参加者の一覧を表示する
            if (0 < response.member_list.length) {

                for (let i = 0; i < response.member_list.length; i++) {
                    const member = response.member_list[i];
                    const li = $('<li>');
                    li.append($('<input>', { type: 'hidden', name: 'user_id', value: member.user_id }));
                    li.append($('<input>', { type: 'hidden', name: 'auth_id', value: member.auth_id }));
                    li.append($('<img>', { class: 'account-icon member-icon', src: member.icon }));
                    li.append($('<div>', { class: 'member-name text-overflow-ellipsis', text: member.name ? member.name : '※未設定' }));
                    if (response.is_manage_user && member.auth_id != 5) {
                        // 管理者の権限は変更させない
                        li.append($('<span>', { class: 'member-modify link button_modify', text: '変更' }));
                    }
                    $('.dialog .board_menu_member .member-list ul').append(li);
                }
                $('.dialog .board_menu_member .member-list').show();
            }

            // 参加申請一覧を表示する
            if (0 < response.join_request_list.length) {

                for (let i = 0; i < response.join_request_list.length; i++) {
                    const joinReq = response.join_request_list[i];
                    const li = $('<li>');
                    li.append($('<img>', { class: 'account-icon member-icon', src: joinReq.icon }));
                    li.append($('<div>', { class: 'member-name text-overflow-ellipsis', text: joinReq.account_name }));
                    const buttonUl = $('<ul>', { class: 'answer-buttons' });
                    buttonUl.data('join_request_id', joinReq.join_request_id);
                    buttonUl.append($('<li>').append($('<button>', { class: 'button bt2 request_ok', text: '許可' })));
                    buttonUl.append($('<li>').append($('<button>', { class: 'button bt2 request_ng', text: '拒否' })));
                    li.append(buttonUl);
                    $('.dialog .board_menu_member .member-request > ul').append(li);
                }
                $('.dialog .board_menu_member .member-request').show();
            }

        }).always(function () {
            stopLoading($('.dialog .dialog-contents-inner'));
        }).fail(function () {
            $('.dialog .close').click();
        });
    }

    /**
     * 参加申請を承認する
     * @param {*} join_request_id 
     * @param {*} accept 
     */
    function acceptMember(join_request_id, accept) {

        const dgb_id = $('.board-top').data('dgb_id');
        const data = {
            dgb_id: dgb_id,
            join_request_id: join_request_id,
            accept: accept,
        };
        console.log('承認処理：', data);
        getResponse('member/answer', data, false, 'POST').done(function (response) {
            // 参加者一覧を最新化する
            console.log(response);
            refreshMember(dgb_id);
        });
    }

    /**
     * 伝言作成ダイアログのイベントを設定する
     */
    function setUpMessageEvents() {

        // 伝言作成ボタン押下イベント
        $('.dialog').on('click', '.create_message button.create', function (e) {
            const dgb_id = $('.board-top').data('dgb_id');
            const data = {
                dgb_id: dgb_id,
                title: $('.dialog .create_message input[type="text"]').val(),
                message: $('.dialog .create_message textarea').val(),
            };

            let isValidateError = false;
            // 未入力チェック
            if (!data.title) {
                setTextError($('.dialog .create_message input[type="text"]'), "入力してください");
                isValidateError = true;
            }

            if (!data.message) {
                setTextError($('.dialog .create_message textarea'), "入力してください");
                isValidateError = true;
            }

            if (isValidateError) return;

            console.log("伝言新規作成：", data);

            $('.dialog').fadeOut(500);

            $('.board-top').empty();
            startLoading($('.board-top'));

            getResponse('message', data, false, 'POST').done(function (response) {
                // 参加者一覧を最新化する
                showBoard(dgb_id, false);
            }).always(function () {
                stopLoading($('.board-top'));
            });

        });
    }

    /**
     * 伝言作成をダイアログ表示する
     * @param {*} dgb_id 
     */
    function showCreateMessage(dgb_id) {

        let template = $('.template.message').clone();
        showCommonDialog('伝言を作成する', template.children());
        // パーフェクトスクロールを設定する
        new PerfectScrollbar('.dialog .create_message .detail-wrapper', { suppressScrollX: true });
        $('.ps__thumb-y').attr('tabindex', -1);
        // タイトルにフォーカスする
        $('.dialog .create_message .message-title input').focus();
    }

    /**
     * 共有ダイアログのイベントを設定する
     */
    function setUpShareEvents() {

        // コピーするボタン押下時のイベント
        $('.dialog').on('click', '.share_board button.share_copy', function (e) {
            if (!copyTextToClipboard($('.dialog .share_board input[type=text]').val())) {
                alert('申し訳ありません。お使いのブラウザには対応していません。');
            }
        });
    }

    /**
     * 共有をダイアログ表示する
     * @param {*} dgb_id 
     */
    function showShare(dgb_id) {
        let template = $('.template.share').clone();
        showCommonDialog('伝言板を共有する', template.children());
        startLoading($('.dialog .dialog-contents-inner'));
        getResponse('share', { dgb_id: dgb_id }, false, 'POST').then(function (response) {

            console.log("共有URL：", response);

            $('.dialog .share_board input[type=text]').val(response.share_url);

            $('.dialog .share_board').css('visibility', 'visible');
        }).always(function () {
            stopLoading($('.dialog .dialog-contents-inner'));
        });
    }

    /**
     * 管理ダイアログのイベントを設定する
     */
    function seUpManageEvents() {
        $('.dialog').on('click', '.board_manage button.button_modify', function (e) {

            // 入力チェック
            if ($('.dialog .board_manage input.join_password').prop('checked') && !$('.dialog .board_manage input.password').val()) {
                setTextError($('.dialog .board_manage input.password'), "入力してください");
                return;
            }

            const dgb_id = $('.board-top').data('dgb_id');
            const joinType = getJoinType(
                $('.dialog .board_manage input.join_approval').prop('checked'),
                $('.dialog .board_manage input.join_password').prop('checked'));
            const defaultAuthId = getAuthID(
                $('.dialog .board_manage input.auth_post').prop('checked'),
                $('.dialog .board_manage input.auth_share').prop('checked'));

            const data = {
                dgb_id: dgb_id,
                join_type: joinType,
                join_password: $('.dialog .board_manage input.password').val(),
                search_type: $('.dialog .board_manage input.search_possible').prop('checked') ? 1 : 0,
                default_auth_id: defaultAuthId,
            };

            console.log("管理メニュー変更：", data);

            startDialogLoading();
            getResponse('manage', data, false, 'PUT').done(function () {
                $('.dialog').fadeOut(500);
            }).always(function () {
                stopDialogLoading();
            });

        });
    }

    /**
     * 伝言板管理をダイアログ表示する
     * @param {*} dgb_id 
     */
    function showManageBoard(dgb_id) {

        let template = $('.template.manage').clone();
        showCommonDialog('管理メニュー', template.children());
        $('.dialog .board_manage .password').parents('li').hide(0);
        startLoading($('.dialog .dialog-contents-inner'));
        
        $('.dialog .board_manage .join_approval').prop('checked', false);
        $('.dialog .board_manage .join_password').prop('checked', false);
        $('.dialog .board_manage .join_password').parents('li').next().hide();
        $('.dialog .board_manage .search_possible').prop('checked', false);
        $('.dialog .board_manage .auth_post').prop('checked', false);
        $('.dialog .board_manage .auth_share').prop('checked', false);

        getResponse('manage', { dgb_id: dgb_id }, false, 'GET').then(function (response) {

            if (response.manage.join_type == 2 || response.manage.join_type == 9) {
                // 作成者が承認するをチェック
                $('.dialog .board_manage .join_approval').prop('checked', true);
            }
            if (response.manage.join_type == 3 || response.manage.join_type == 9) {
                // パスワードで承認するをチェック
                $('.dialog .board_manage .join_password').prop('checked', true);
                // パスワードを表示する
                $('.dialog .board_manage .password').val(response.manage.join_password);
                $('.dialog .board_manage .join_password').parents('li').next().show();
            }

            if (response.manage.search_type == 1) {
                // 検索で表示するをチェック
                $('.dialog .board_manage .search_possible').prop('checked', true);
            }

            console.log("管理情報取得：", response);
            if (response.manage.default_auth_id == 2 || 4 <= response.manage.default_auth_id) {
                // 投稿権限をチェック
                $('.dialog .board_manage .auth_post').prop('checked', true);
            }
            if (response.manage.default_auth_id == 3 || 4 <= response.manage.default_auth_id) {
                // 投稿権限をチェック
                $('.dialog .board_manage .auth_share').prop('checked', true);
            }

            setTimeout(function () {
                stopLoading($('.dialog .dialog-contents-inner'));
                $('.dialog .board_manage').css('visibility', 'visible');
            }, 300);
        }).fail(function () {
            closeCommonDialog();
            stopLoading($('.dialog .dialog-contents-inner'));
        });
    }

    /**
     * 伝言板参加ページのイベントを設定する
     */
    function setUpJoinBoardEvents() {

        // 登録ボタン押下イベント
        $('.board-top').on('click', '.join-board-page button.join', function (e) {

            const data = {
                dgb_id: $('.board-top').data('dgb_id'),
                name: $('.board-top .join-board-page input[type=text]').val(),
            };

            if (!data.name) {
                setTextError($('.board-top .join-board-page input[type=text]'), "入力してください");
                return;
            }

            console.log("参加者名登録処理：", data);

            $('.board-top').empty();
            startLoading($('.board-top'));

            getResponse('member', data, false, 'POST').then(function (response) {
                // 伝言板ページを表示する
                showBoard(data.dgb_id, false);
            }).fail(function () {
                stopLoading();
            });

        });

        var input_join_member_name = "";
        // アカウント名を使用するチェックボックス変更時のイベント
        $('.board-top').on('change', '.join-board-page input[type="checkbox"]', function (e) {
            if (e.target.checked) {
                input_join_member_name = $('.board-top .join-board-page input[type=text]').val();
                $('.board-top .join-board-page input[type=text]').val($('header .account-name').text());
                $('.board-top .join-board-page input[type=text]').removeClass('text-error');
                $('.board-top .join-board-page input[type=text]').parent().removeClass('text-error');
            } else {
                $('.board-top .join-board-page input[type=text]').val(input_join_member_name);
            }
        });

        // 名前テキストボックスの入力時のイベント
        $('.board-top').on('input', '.join-board-page input[type=text]', function (e) {
            if ($('.board-top .join-board-page input[type="checkbox"]').prop('checked')) {
                // アカウント名を使用するチェックがONの場合
                const inputVal = $('.board-top .join-board-page input[type=text]').val();
                // 入力した名前がアカウント名と異なる場合、チェックボックスをOFFにする
                if (inputVal != $('header .account-name').text()) {
                    $('.board-top .join-board-page input[type="checkbox"]').prop('checked', false);
                }
            }
        });
    }
}