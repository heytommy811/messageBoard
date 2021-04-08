var searchKeyword = '';
/**
 * 検索画面を表示する
 */
function showSearch(keyword, skipHistory) {

    // 履歴を追加
    if (!skipHistory) {
        addHistory('?page=search&keyword=' + (keyword === undefined ? '' : keyword));
    }

    closeBoard();
    $('.content.search').show();
    const left = $('.content.search').css('left').replace('px', '');
    if (0 < left) {
        // 検索を表示する
        $('.content.search').animate({ left: 0 }, 200);
        $('header .menu li').removeClass('active');
        $('header .menu li.search').addClass('active');
        $('.search-keyword input').val(keyword);
        if (keyword !== undefined) {
            searchKeyword = keyword;
            search({
                keyword: keyword,
                skipHistory: true,
                currentPageIndex: 0
            });
        }
        // 検索キーワードにフォーカスインする
        setTimeout(function () {
            $('.search-keyword input').focus();
        }, 200);
        $('.massage-board-list').hide(200);
    }
}

/**
 * 検索のセットアップ
 */
function setUpSearch() {

    // ヘッダーメニューの検索ボタン押下時
    $('header .menu li.search').click(function () {
        showSearch(undefined, false);
    });

    // 検索ボタン押下時のイベント
    $('span.search-icon').click(function () {
        search({
            keyword: $('.search-keyword input').val(),
            skipHistory: false,
            currentPageIndex: 0
        });
    });
    // エンターキーを押下したときのイベント
    $('.search-keyword input').keypress(function (e) {
        if (e.keyCode === 13) {
            $('span.search-icon').click();
        }
    });
    // ページャーのボタン押下時
    $(document).on('click', '.page-btn', function () {
        if ($(this).hasClass('current') || $(this).hasClass('disable')) return;
        let page = $(this).data('page');
        let currentPage = $('.page-btn.current').data('page');
        if (page === 'next') {
            page = currentPage + 1;
        } else if (page === 'prev') {
            page = currentPage - 1;
        }
        search({
            keyword: searchKeyword,
            skipHistory: false,
            currentPageIndex: page
        });
    });

    // 参加ボタンイベント
    $('.search-results').on('click', 'button.join', function (e) {
        const _searchResult_obj = $(this).parents('li');
        const dgb_id = _searchResult_obj.find('[name=dgb_id]').val();
        const join_type = _searchResult_obj.find('[name=join_type]').val();
        if (join_type == 3 || join_type == 9) {

            // パスワード認証ダイアログを表示する
            let template = $('.template.join').clone();
            $('.dialog .dialog-contents-inner').empty();
            $('.dialog .dialog-contents-inner').append(template.children());
            $('.dialog .title').text('参加パスワード');
            $('body .dialog').fadeIn(200);
            $('.dialog input[type=hidden]').val(dgb_id);
            $('.dialog .join_dengonban input').focus();

        } else {

            let data = {
                dgb_id: dgb_id,
            };
            joinRequest(data);
        }
    });

    // 参加認証のダイアログボタンイベント
    $('.dialog').on('click', '.join_dengonban button', function (e) {
        const password = $('.dialog .join_dengonban input.password').val();
        if (!password) {
            setTextError($('.dialog .join_dengonban input.password'), "入力してください");
            return;
        }
        const dgb_id = $('.dialog input[type=hidden]').val();
        const data = {
            dgb_id: dgb_id,
            password: password,
        };
        joinRequest(data);
        // ダイアログを閉じる
        $('.dialog').fadeOut(200);
    });

    /**
     * 参加処理
     * @param {*} data 
     */
    function joinRequest(data) {

        console.log("参加処理", data);

        startLoading();
        getResponse('member/request', data, false, 'POST').done(function (response) {
            let obj = $('.search-results li').filter(function () {
                return $(this).find('[name=dgb_id]').val() == data.dgb_id;
            });

            if (response.result == 'joined') {

                // 参加済み
                obj.find('button').text('参加済');
                obj.find('button').removeClass('reaction');
                obj.find('button').removeClass('join');
                obj.find('button').addClass('member');
                obj.find('button').attr('tabindex', -1);

            } else if (response.result == 'requested') {
                // 申請中
                obj.find('button').text('申請中');
                obj.find('button').removeClass('reaction');
                obj.find('button').removeClass('join');
                obj.find('button').addClass('request');
                obj.find('button').attr('tabindex', -1);
                showAlert('参加申請を送信しました。');
            }
        }).always(function () {
            stopLoading();
        });
    }
}

/**
 * 検索処理
 */
function search(param) {

    console.log("検索キーワード：", param.keyword);

    // 履歴を追加
    if (!param.skipHistory) {
        replaceHistory('?page=search&keyword=' + param.keyword);
    }

    let data = {
        keyword: param.keyword,
        p: param.currentPageIndex
    };
    startLoading();
    getResponse('search/board', data, false, 'GET').done(function (result) {
        // 検索結果を表示する
        refreshPage(result.total_page, param.currentPageIndex);
        refreshSearchResults(result.board_list);
    }).always(function () {
        stopLoading();
    });
}

/**
 * 検索結果をリフレッシュします
 * @param {Array} board_list 
 */
function refreshSearchResults(board_list) {
    $('.search-result-board').empty();
    let delay = 0;
    $.each(board_list, function (i, board) {
        let template1 = $('.content.search .template .label').clone();
        // template1.find('.creater').text(board.create_user_name);
        template1.find('.creater').text(board.name);
        template1.find('.title').text(board.title);
        let template2 = $('.template .join-button').clone();
        if (board.button_type == '1') {
            // 参加
            template2.find('button').addClass('reaction');
            template2.find('button').addClass('join');
            template2.find('button').text('参加');
        } else if (board.button_type == '2') {
            // 申請中
            template2.find('button').addClass('request');
            template2.find('button').text('申請中');
            template2.find('button').attr('tabindex', -1);
        } else if (board.button_type == '3') {
            // 参加済
            template2.find('button').addClass('member');
            template2.find('button').text('参加済');
            template2.find('button').attr('tabindex', -1);
        }
        let li = $('<li>');
        li.append($('<input>', { type: 'hidden', name: 'dgb_id', value: board.dgb_id }));
        li.append($('<input>', { type: 'hidden', name: 'join_type', value: board.join_type }));
        li.append(template1).append(template2);
        setTimeout(function () {
            $('.search-result-board').append(li);
        }, delay);
        delay += 100;
    });
    setTimeout(function () {
        $('.search-result-page.foot').fadeIn(500);
    }, delay)
}

/**
 * ページャーを更新する
 * @param {Number} totalPage トータルページ件数
 * @param {Number} currentPageIndex 現在のページ
 */
function refreshPage(totalPage, currentPageIndex) {
    $('.search-result-page > ul').empty();
    if (totalPage <= 1) {
        $('.search-result-page-wrapper').hide();
        return;
    } else {
        $('.search-result-page-wrapper').show();
    }

    for (let i = 0; i < totalPage; i++) {
        $('.search-result-page > ul').append('<li data-page="' + i + '" class="page-btn ' + (i === currentPageIndex ? 'current' : '') + '">' + (i + 1) + '</li>');
    }
    $('.search-result-page-prev').removeClass('disable');
    $('.search-result-page-next').removeClass('disable');
    if (currentPageIndex === 0) {
        // 前へを非活性に
        $('.search-result-page-prev').addClass('disable');
    } else if (currentPageIndex === totalPage - 1) {
        // 次へを非活性に
        $('.search-result-page-next').addClass('disable');
    }
    $('.search-result-page.foot').hide();
}

