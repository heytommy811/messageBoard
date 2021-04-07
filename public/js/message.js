
/**
 * 伝言を表示する
 * @param {*} dgn_id 
 */
function showMessage(dgn_id, skipHistory) {

    // 伝言ページを開く
    openBoardPage();

    if (!skipHistory) {
        // 履歴を追加する
        addHistory('?page=message&m=' + dgn_id);
    }

    if ($('.board-top').find('.loading-wrapper').length === 0) {
        $('.board-top').empty();
        startLoading($('.board-top'));
    }

    getResponse('message', { dgn_id: dgn_id }, true, 'GET').then(function (response) {
        if (response.message) {
            
            if (response.status == 'timeout') {
                // ログインへリダイレクトする
                showAlert(response.message, function() {
                    redirectAppRoot()
                });
            } else if (response.status == 'error' && response.message) {
                // エラーメッセージを表示する
                showAlert(response.message, function() {
                    showBoard($('.board-top').data('dgb_id'), false);
                });
            }
            
        } else {
            $('.board-top').append(response);
            $('.board-top').data('dgn_id', dgn_id);
        }
    }).always(function () {
        stopLoading($('.board-top'));
    });
}

/**
 * 伝言メッセージのセットアップ
 */
function setUpMessage() {

    // コメント・いいねボタン押下イベント
    $('.board-top').on('click', '.message-footer > li > span', function (e) {
        if ($(this).hasClass('comment-icon')) {
            // コメントダイアログを表示する
            showComment();
        } else if ($(this).hasClass('comment-like')) {
            // いいね更新処理
            updateLike();
        }
    });

    // 三点リーダーの押下イベント
    $('.board-top').on('click', '.option', function (e) {
        if ($(this).hasClass('active')) {
            $(this).find('.option-shadow').fadeOut(200);
        } else {
            $(this).find('.option-shadow').fadeIn(200);
        }
        $(this).toggleClass('active');
    });

    // 吹き出しの編集・削除の押下イベント
    $('.board-top').on('click', '.option-shadow li', function (e) {
        const dgn_id = $('.board-top').data('dgn_id');
        if ($(this).hasClass('modify_message')) {
            showModifyMessage();
        } else if ($(this).hasClass('delete_message')) {
            showConfirm("伝言を削除してよろしいですか？", function () {
                deleteMessage(dgn_id);
            });
        }
    });

    // 編集ダイアログの保存ボタン押下イベント
    $('.dialog').on('click', '.modify_message button.save', function (e) {
        const dgn_id = $('.board-top').data('dgn_id');
        const data = {
            dgn_id: dgn_id,
            title: $('.dialog .modify_message input[type=text]').val(),
            message: $('.dialog .modify_message textarea').val(),
        };

        let isValidateError = false;
        // 未入力チェック
        if (!data.title) {
            setTextError($('.dialog .modify_message input[type=text]'), "入力してください");
            isValidateError = true;
        }

        if (!data.message) {
            setTextError($('.dialog .modify_message textarea'), "入力してください");
            isValidateError = true;
        }

        if (isValidateError) return;

        console.log("伝言内容編集：", data);

        $('.dialog').fadeOut(500);
        startLoading();

        getResponse('message', data, true, 'PUT').then(function (response) {
            console.log(response);
            
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
                $('.board-top').empty();
                $('.board-top').append(response);
            }
        }).always(function () {
            stopLoading();
        });
    });

    // コメント送信ボタン押下イベント
    $('.dialog').on('click', '.comment button.send', function (e) {

        if (!$('.dialog .comment textarea').val()) {
            setTextareaError($('.dialog .comment textarea'), 'コメントを入力してください');
            return;
        }

        const data = {
            dgn_id: $('.board-top').data('dgn_id'),
            comment: $('.dialog .comment textarea').val(),
        };

        const n = Number($('.count_comment').text());
        $('.count_comment').text(n + 1);
        $('.comment-icon').addClass('send');

        console.log("コメント送信処理：", data);
        startDialogLoading();
        getResponse('comment', data, false, 'POST').then(function (response) {
            refreshCommentList(response);
            closeCommonDialog();
        }).always(function () {
            stopDialogLoading();
        });

    });

    // コメント削除ボタン押下イベント
    $('.board-top').on('click', '.comment .delete-comment-icon', function (e) {

        const data = {
            dgn_id: $('.board-top').data('dgn_id'),
            cmt_id: $(e.target).data('cmt_id'),
        };

        console.log("コメント削除処理：", data);
        startLoading();
        getResponse('comment', data, false, 'DELETE').then(refreshCommentList).always(function () {
            stopLoading();
        });

    });

    /**
     * コメント欄をリフレッシュする
     */
    function refreshCommentList(response) {
        // コメント件数を更新
        $('.count_comment').text(response.comment_list ? response.comment_list.length : '0');
        $('.comment-list').remove();
        $('.message-page .comment').append($('<ul>', { class: 'comment-list' }));
        for (let i = 0; i < response.comment_list.length; i++) {
            const comment = response.comment_list[i];
            const li = $('<li>');
            li.append($('<input>', { type: 'hidden', name: 'cmt_id', value: comment.cmt_id }));
            li.append($('<div>', { class: 'comment-left' }).append($('<img>', { class: 'account-icon', src: comment.icon })));
            const right = $('<div>', { class: 'comment-right' });
            const header = $('<div>', { class: 'comment-header' });

            header.append($('<div>', { class: 'name text-overflow-ellipsis', text: comment.name }));
            header.append($('<div>', { class: 'date', text: comment.post_dt }));
            if (comment.can_delete) {
                header.append($('<div>', { class: 'cmn-spread-icon delete-comment-icon', title: "コメントを削除する", 'data-cmt_id': comment.cmt_id }));
            }
            right.append(header);
            right.append($('<div>', { class: 'comment-detail', text: comment.comment }));
            li.append(right);
            $('.comment-list').append(li);
        }
    }

    /**
     * コメントダイアログを表示する
     */
    function showComment() {
        let template = $('.template.comment').clone();
        showCommonDialog('コメントする', template.children());
        // パーフェクトスクロールを設定する
        new PerfectScrollbar('.dialog .comment .comment-wrapper', { suppressScrollX: true });
        $('.ps__thumb-y').attr('tabindex', -1);
        // テキストエリアにフォーカスする
        $('.dialog .comment textarea').focus();
    }

    /**
     * いいね更新処理
     */
    function updateLike() {
        const data = {
            dgn_id: $('.board-top').data('dgn_id')
        };

        console.log("いいね更新処理：", data);

        // 処理結果を待たずに先に表示を切り替える
        const n = Number($('.count_iine').text());
        if ($('.comment-like').hasClass('liked')) {
            $('.comment-like').removeClass('liked');
            $('.count_iine').text(n - 1);
        } else {
            $('.comment-like').addClass('liked');
            $('.count_iine').text(n + 1);
        }

        getResponse('like', data, false, 'POST').then(function (response) {
            if (response.liked) {
                $('.comment-like').addClass('liked');
            } else {
                $('.comment-like').removeClass('liked');
            }
            // いいね件数を更新
            $('.count_iine').text(response.count_like);
        });
    }

    /**
     * 伝言編集ダイアログを表示する
     */
    function showModifyMessage() {
        let template = $('.template.modify_message').clone();
        showCommonDialog('編集する', template.children());
        // 伝言の内容を初期表示する
        $('.dialog .modify_message input[type=text]').val($('.message-page .message-title').text());
        $('.dialog .modify_message textarea').val($('.message-page .message-detail').text());
        // パーフェクトスクロールを設定する
        new PerfectScrollbar('.dialog .modify_message .detail-wrapper', { suppressScrollX: true });
        $('.ps__thumb-y').attr('tabindex', -1);
    }

    /**
     * 伝言を削除する
     * @param {*} dgn_id 
     */
    function deleteMessage(dgn_id) {

        $('.board-top').empty();
        startLoading($('.board-top'));

        const data = { dgn_id: dgn_id };
        getResponse('message', data, false, 'DELETE').then(function (response) {
            // 伝言板のページを表示する
            showBoard($('.board-top').data('dgb_id'), false);
        });

    }

}
