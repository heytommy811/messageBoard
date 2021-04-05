/**
 * ローディング開始
 */
function startLoading(dom) {
    if (!dom) {
        $('#header-inner .logo').addClass('loading');
    } else {
        // 二重で表示しない
        if (dom.find('.loading-wrapper').length === 0) {
            const loader = $('<ul>', { class: 'content-loading' }).append($('<li>')).append($('<li>'));
            dom.append($('<div>', { class: 'loading-wrapper' }).append(loader));
        }
    }
}

/**
 * ダイアログ用のローディング開始
 */
function startDialogLoading() {
    const loader = $('<ul>', { class: 'content-loading' }).append($('<li>')).append($('<li>'));
    $('.dialog .dialog-contents').append($('<div>', { class: 'dialog-loading-wrapper' }).append($('<div>', { class: 'loading-wrapper' }).append(loader)))
}

/**
 * ローディング終了
 */
function stopLoading(dom) {
    if (!dom) {
        $('#header-inner .logo').removeClass('loading');
    } else {
        dom.find('.loading-wrapper').remove();
    }
}

/**
 * ダイアログ用のローディング終了
 */
function stopDialogLoading() {
    $('.dialog-loading-wrapper').remove();
}

/**
 * 操作をブロックするローディングを表示する
 */
function startBlockLoading() {
    $('body').append($('<div>', { class: "block-loading-content" }));
    startLoading($('.block-loading-content'));
}
/**
 * 操作ブロックのローディングを解除する
 */
function stopBlockLoading() {
    $('.block-loading-content').remove();
}

/**
 * http get request
 * @param {string} route 
 */
function getRequest(route) {
    startLoading();
    setTimeout(function () {
        window.location.href = getUrl(route);
    }, 100);
    return false;
}

/**
 * formタグでサブミットする
 * @param {*} form 
 * @param {*} method 
 * @param {*} action 
 * @param {*} param - パラメータ(任意)
 */
function formSubmit(wrap_dom, method, action, param) {
    startLoading();
    if (wrap_dom) {
        let form = $('<form />', { method: method, action: param ? getUrl(action + '/' + param) : getUrl(action) });
        wrap_dom.wrap(form);
        form.submit();
    } else {
        setTimeout(function () {
            $('form').submit();
        }, 100);
    }
    return false;
}

/**
 * POSTリクエストを非同期で送信し、レスポンスを取得する共通処理
 * @param {*} url 
 * @param {*} data 
 */
function getResponse(url, data, onlyReturn, method) {
    let d = $.Deferred();

    // 非同期でコメントの投稿を行い、最新のコメント一覧を取得する
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('[name="_token"]').val(),
            'X-HTTP-Method-Override': method
        }
    });

    let param;
    if (url instanceof Object) {
        param = url;
    } else {
        if (method && method !== 'GET') {
            data['_method'] = method;
        }
        param = {
            url: getUrl(url),
            type: method == 'GET' ? method : 'POST',
            data: data
        };
    }
    $.ajax(param)
        .done(function (result) {
            if (onlyReturn) {
                d.resolve(result);
                return;
            }
            if (result.status === 'timeout') {
                // ログイン画面へリダイレクトする
                showAlert(result.message, function() {
                    redirectAppRoot();
                });
            } else if (result.status === 'error') {
                if (result.message) {
                    showAlert(result.message, function () {
                        d.reject(result);
                    });
                } else {
                    showAlert('サーバーでエラーが発生しました。', function () {
                        d.reject(result);
                    });
                }
            } else {
                // 正常終了
                d.resolve(result);
            }
        })
        .fail(function (result) {
            let message = '';
            if (result.status == 422) {
                // バリデーションエラーの場合
                let validationMessages = [];
                console.error('validation error', result.responseJSON.errors)
                for (const paramName in result.responseJSON.errors) {
                    validationMessages = validationMessages.concat(result.responseJSON.errors[paramName]);
                }
                message = validationMessages.join('<br>');
                showAlert(message, function () {
                    d.reject({ validationError: true });
                });
                return;
            }
            console.error(result);
            showAlert('予期せぬエラーが発生しました。', function () {
                d.reject(result);
            });
        });
    return d.promise();
}

/**
 * アプリケーションのルートURLへリダイレクトさせる
 */
function redirectAppRoot() {
    window.location.href = $('[name=base_url]').val();
}

function getUrl(action) {
    return $('[name=base_url]').val() + '/' + action;
}
/**
 * メッセージダイアログ表示共通処理
 * @param {string} message 
 */
function showAlert(message, callback) {
    $('#alert_dialog .alert-message').empty();
    $('#alert_dialog .alert-message')[0].innerHTML = message;
    $('#alert_dialog').show(0);
    $('#alert_dialog .dialog-contents').addClass('show');
    // 表示中のダイアログの背景を透過
    // $('.dialog').addClass('transparent');
    if (callback) {
        $('#alert_dialog .dialog-contents .alert-button').click(function (e) {
            callback();
        });
    }
}

/**
 * 確認ダイアログを表示する共通処理
 * @param {*} message 
 * @param {*} callback 
 */
function showConfirm(message, callback) {
    $('#confirm_dialog .dialog-contents').empty();
    $('#confirm_dialog .dialog-contents').append($('<div>', { class: 'confirm-message' }));
    $('#confirm_dialog .dialog-contents .confirm-message')[0].innerHTML = message;
    const buttons = $('<div>', { class: 'confirm-button' });
    buttons.append($('<div>', { class: 'confirm-ok-button', text: 'はい' }));
    buttons.append($('<div>', { class: 'confirm-no-button', text: 'いいえ' }));
    $('#confirm_dialog .dialog-contents').append(buttons);
    $('#confirm_dialog').show(0);
    $('#confirm_dialog .dialog-contents').addClass('show');
    // イベントを設定
    $('#confirm_dialog .dialog-contents .confirm-button').click(function (e) {
        $('#confirm_dialog .dialog-contents').removeClass('show');
        setTimeout(function () {
            $('#confirm_dialog').fadeOut(200);
            // 表示中のダイアログの背景を戻す
            $('.dialog').removeClass('transparent');
        }, 250);
        // コールバック関数を呼び出す
        if ($(e.target).hasClass('confirm-ok-button')) {
            callback();
        }
    });
}

/**
 * 共通ダイアログを表示する
 * @param {*} title 
 * @param {*} content 
 */
function showCommonDialog(title, content) {
    $('.dialog .dialog-contents-inner').empty();
    $('.dialog .dialog-contents-inner').append(content);
    $('.dialog > .dialog-contents > .title').text(title);
    $('body .dialog').fadeIn(200);
}

function closeCommonDialog() {
    $('body .dialog').fadeOut(200);
}

/* onLoad時に行う処理 */
$(document).ready(function () {

    // クリック全般
    $(document).on('click', function (e) {
        // ヘッダーのアイコン以外をクリックした場合
        if (!$(e.target).hasClass('header-icon') && !$(e.target).hasClass('account')) {
            if (!$(this).hasClass('account-menu')) {
                $('header .account .account-menu').removeClass('show');
            }
        }
        // メッセージのオプション以外の場合
        if (!$(e.target).hasClass('option-shadow') && !$(e.target).hasClass('option')) {
            $('.option-shadow').fadeOut(200);
            $('.message .option').removeClass('active');
        }
    });

    $('header .account-menu').on('click', function (e) {
        console.log($(e.target));
        if ($(e.target).hasClass('create')) {
            showCreateBoard();
        } else if ($(e.target).hasClass('change_profile')) {
            showChangeProfile();
        } else if ($(e.target).hasClass('change_password')) {
            showChangePassword();
        }
        $('header .account .account-menu').toggleClass('show');
    });

    // ヘッダーのアカウント押下時のイベント
    $('header .header-icon').click(function () {
        $('header .account .account-menu').toggleClass('show');
    });

    // ダイアログの閉じるイベント
    $('.dialog .close').click(function () {
        $(this).parents('.dialog').fadeOut(200);
    });

    // アラートダイアログの閉じるイベント
    $('.alert-dialog .alert-button').click(function () {
        $(this).parents('.dialog-contents').removeClass('show');
        setTimeout(function () {
            $('.alert-dialog').fadeOut(200);
            // 表示中のダイアログの背景を戻す
            $('.dialog').removeClass('transparent');
        }, 250);
    });

    // ダイアログのテキストのエラー解除
    $(document).on('input', 'input[type=text], input[type=email], input[type=password]', function (e) {
        $(this).removeClass('text-error');
        $(this).parent().removeClass('text-error');
    });
    // ダイアログのテキストエリアのエラー解除
    $('.dialog').on('input', 'textarea', function (e) {
        $(this).removeClass('text-error');
        $(this).parent().removeClass('text-error');
    });
    $('.dialog').on('input', 'input[type=password]', function (e) {
        $(this).removeClass('text-error');
        $(this).parent().removeClass('text-error');
    });
    // テキストエリアのサイズ自動調整
    $('.dialog').on('input', 'textarea', function (e) {
        autoHeightTextarea(e);
    });

    // お知らせのリンクが押下された
    $('.massage-board-list').on('click', 'ul.infomation li div', function () {
        const dgb_id = $(this).data('dgb_id');
        const dgn_id = $(this).data('dgn_id');

        if (dgb_id) {
            // 伝言板ページへ
            showBoard(dgb_id, false);
        } else if (dgn_id) {
            // 伝言ページへ
            showMessage(dgn_id);
        }
    });

    // ブラウザの戻る進む
    window.onpopstate = function(e) {
        console.log(location);
        initialize();

    };
});

/**
 * ページロード時の初期処理
 */
function initialize() {
    
    const query = location.search.replace('?', '');
    // クエリがない場合はホームを表示する
    if (!query) {
        showHome(true);
        return;
    }

    // クエリを連想配列にする
    const parameters = {};
    const tmp = query.split('&');
    tmp.forEach(function(v, i) {
        let parameter = v.split('=');
        parameters[parameter[0]] = decodeURI(parameter[1]);
    });
    console.log(parameters);

    // pageパラメータの値によって画面を表示する
    switch(parameters.page) {
        case 'board':
            showBoard(parameters.d, true);
            break;
        case 'message':
            showMessage(parameters.m, true);
            break;
        case 'search':
            showSearch(parameters.keyword, true);
            break;
        default:
            console.error('不正なURLです。');
            break;
    };
}

/**
 * ホームを表示する
 */
function showHome(skipHistory) {
    $('.content.board .error-message.show').removeClass('show');
    refreshTop();
    $('header .menu li').removeClass('active');
    $('header .menu li.home').addClass('active');
    $('.content.search').animate({ left: '100%' }, 200);
    closeBoard();
    if (!skipHistory) {
        addHistory('');
    }
}

/**
 * 表示ページの履歴を追加する
 * @param {*} url 
 */
function addHistory(query) {
    const url = location.pathname + query;
    history.pushState(null, null, encodeURI(url));
}

/**
 * 履歴の書き換えを行う
 * @param {*} query 
 */
function replaceHistory(query) {
    const url = location.pathname + query;
    history.replaceState(null, null, encodeURI(url));
}

/**
 * 伝言ページを閉じる
 */
function closeBoard() {
    $('.content.board').animate({ top: '100%' }, 200);
    setTimeout(function () {
        $('.content.board').hide();
    }, 200);
}

/**
 * トップページをリフレッシュする
 */
function refreshTop() {
    startLoading();
    return getResponse('board/all', {}, false, 'GET').done(function (res) {
        // 表示する
        $('.massage-board-list').show();
        $('.massage-board-list').empty();
        $('.massage-board-list').append(res);
    }).always(function (e) {
        stopLoading();
    });
}

/* 共通処理メソッド */
/**
 * 文字列フォーマットメソッド
 * @param {any} displayText
 * @param {any} formats
 */
function stringFormat(displayText, formats) {
    if (formats) {
        for (var i = 0, l = formats.length; i < l; i++) {
            var target = '{' + i + '}';
            var replaced = formats[i];
            while (displayText.indexOf(target) >= 0) {
                displayText = displayText.replace(target, replaced);
            }
        }
    }
    return displayText;
}

/**
 * パンくずリストを作成する
 * @param {any} labels - 表示ラベル名の配列
 * @param {any} ids - 画面IDの配列（遷移先）
 */
function createBreadCrumbList(labels, ids) {
    if ($('.bread-crumb-list')) {
        var html = '<ul class="bread-crumb-ul">';
        for (var i = 0; i < labels.length; i++) {
            if (labels.length - 1 === i) {
                html += '<li class="bread-crumb-li current">' + labels[i] + '</li>';
            } else {
                html += '<li class="bread-crumb-li link" onclick="getRequest(' + "'" + ids[i] + "'" + ')">' + labels[i] + '</li>';
                html += '<li class="bread-crumb-li">&gt&gt</li>';
            }

        }
        html += '</ul>';
        $('.bread-crumb-list').append(html);
    }
}

// 起動中のダイアログID
dialog_id = null;

/**
 * ダイアログを表示する
 * @param {any} id
 * * @param {bool} moderess
 */
function showDialog(id, moderess) {
    if (dialog_id) return;
    if (!moderess) {
        dialog_id = id;
    }
    $('#' + id).dialog('open');
    $('#' + id).focus();
    return true;
}
/**
 * ダイアログを閉じる
 * @param {any} id
 */
function hideDialog(id) {
    if (id) {
        $('#' + id).dialog('close');
    } else {
        $('#' + dialog_id).dialog('close');
        dialog_id = null;
    }
}

/**
 * ダイアログをセットアップする
 * @param {any} id
 * @param {any} option
 */
function setupDialog(id, option) {
    $('#' + id).dialog({
        autoOpen: false,
        resizable: false,
        modal: option.modal ? true : false,
        dialogClass: 'jquery-ui-dialog-common' + (option.dialogClass ? ' ' + option.dialogClass : ''),
        show: 'clip',
        hide: 'clip',
        width: option.width ? option.width : '300',
        height: option.height ? option.height : '200',
        beforeClose: option.beforeClose,
        close: function () {
            // 閉じるボタンでダイアログを閉じた場合にダイアログIDを初期化
            dialog_id = null;
        }
    });
    //var cancel_button = $('#' + id + ' .button_cancel');
    var cancel_button = $('#' + id + ' .button_cancel');
    if (cancel_button) {

        cancel_button.click(function () {
            if ($.type(option.cancel) === 'function') {
                var result = option.cancel();
                if (result) {
                    hideDialog();
                }
            } else {
                hideDialog();
            }
        });
    }
}

/**
 * 権限チェックボックスの切替イベント
 */
function setAuthCheckBoxEvents() {
    cbx_auth1_checked = false;
    cbx_auth2_checked = false;
    // デフォルト権限チェックボックスの切替イベント
    $('#checkbox-auth-3').on('change', function (e) {
        if (e.target.checked) {
            cbx_auth1_checked = $('#checkbox-auth-1').prop('checked');
            cbx_auth2_checked = $('#checkbox-auth-2').prop('checked');
            // 管理権限が指定された場合、すべての権限チェックボックスを選択し、非活性にする
            if (!$('#checkbox-auth-1').prop('checked')) $('#checkbox-auth-1').click();
            $('#checkbox-auth-label-1').addClass('ui-state-disabled');
            $('#checkbox-auth-1').prop('disabled', true);
            if (!$('#checkbox-auth-2').prop('checked')) $('#checkbox-auth-2').click();
            $('#checkbox-auth-label-2').addClass('ui-state-disabled');
            $('#checkbox-auth-2 ').prop('disabled', true);
        } else {
            $('#checkbox-auth-1').prop('disabled', false);
            $('#checkbox-auth-label-1').removeClass('ui-state-disabled');
            if (!cbx_auth1_checked) $('#checkbox-auth-1').click();
            $('#checkbox-auth-2').prop('disabled', false);
            $('#checkbox-auth-label-2').removeClass('ui-state-disabled');
            if (!cbx_auth2_checked) $('#checkbox-auth-2').click();
        }
    });
}

/**
 * テキストボックスからclassを削除するイベントを設定する
 * @param {any} textObj - テキストボックスのオブジェクト
 * @param {any} removeClass - 削除するclass名
 */
function setRemoveClassTextInputEvent(textObj, removeClass) {
    textObj.on('input', function (e) {
        textObj.removeClass(removeClass);
        textObj.parent().removeClass(removeClass);
    });
}

/**
 * パスワードの表示制御イベントを設定する
 */
function setPasswordSliderEvent() {
    if ($('input[name="radio"]:checked').val() == 'permission') {
        $('.new-board-input-li.password').css('height', 0);
    }
    $('input[name="radio"]:radio').on('change', function (e) {
        $('.new-board-input-li.password .board-manage-item').slideToggle(500);
    });
}

/**
 * テキストエリアの高さを自動調整する
 * @param {*} e 
 */
function autoHeightTextarea(e) {
    const lineHeight = parseInt($(e.target).css('lineHeight'));
    const minHeight = parseInt($(e.target).css('min-height'));
    if (e.target.offsetHeight < e.target.scrollHeight) {
        // エリアを高くする場合
        $(e.target).height(e.target.scrollHeight);
    } else {
        // エリアを短くする場合
        while (true) {
            $(e.target).height(parseInt($(e.target).height()) - lineHeight);
            if (e.target.offsetHeight < e.target.scrollHeight) {
                $(e.target).height(e.target.scrollHeight);
                break;
            }
            if (parseInt($(e.target).height()) <= minHeight) {
                break;
            }
        }
    }
}

/**
 * ファイル選択イベントを設定する
 */
function setFileSelectEvent() {
    // ファイル選択ボタンクリックイベント
    $('.dialog').on('click', '.icon', function () {
        $(this).prev().click();
        return false;
    });

    // ファイル選択時のイベント
    $('.dialog ').on('change', 'input[type=file]', function () {

        var file = this.files[0];
        if (!file.type.match(/^image\/(png|jpeg|gif)$/)) return;

        startLoading();
        let width_d = $(window).width();
        let height_d = $(window).height();
        console.info(width_d)
        console.info(height_d)
        if (500 < width_d) {
            width_d = width_d < 600 ? 400 : $(window).width() - 200;
            height_d = height_d < 600 ? 400 : $(window).height() - 400;
            if (width_d < height_d) {
                // 横を基準にダイアログの大きさを変える
                height_d = (width_d / 4) * 3;
            } else {
                // 縦を基準に横の大きさを変える
                width_d = (height_d / 3) * 4;
            }
        }
        console.info(width_d)
        console.info(height_d)
        
        var reader = new FileReader();
        reader.onload = function () {
            // TODO: EXIF情報を取得するためにライブラリを使用する
            setIconSizeChangeDialog(reader.result, width_d, height_d);
        }
        reader.readAsDataURL(file);
        return;
    });
}

/**
 * canvasにアイコン画像を表示する
 * @param {*} base64 
 */
function setCanvasImage(icon) {
    if (!icon.src) {
        return;
    }

    var canvas = $(".dialog canvas");
    var ctx = canvas[0].getContext("2d");
    var image = new Image();
    image.onload = function () {
        $(".dialog canvas").show();
        $(".dialog img").hide();
        // NOTE: 0,0,150,150はcanvasじょうのx,y,width,heightを表す
        ctx.drawImage(image, icon.x, icon.y, icon.w, icon.h, 0, 0, 150, 150);
        canvas.parents('.icon').addClass('selected');
    }
    image.src = icon.src;
}

/**
 * アイコンサイズ変更ダイアログを設定する
 * @param {*} file_path 
 */
function setIconSizeChangeDialog(file_path, width_d, height_d) {

    // サイズ調整する画像要素を毎回クリアする
    $('.icon-preview').empty();

    if (!file_path) {
        return false;
    }

    // 元画像の大きさに応じてサイズを変更する
    let cropper_image = new Image();

    cropper_image.onload = function (e) {

        // アップロードした画像のサイズ
        let w = cropper_image.naturalWidth;
        let h = cropper_image.naturalHeight;

        console.log("icon-preview:", width_d, height_d);
        console.log("cropper_image:", w, h);
        $('.cropper-dialog .icon-preview').css('max-width', width_d);
        $('.cropper-dialog .icon-preview').css('max-height', height_d);

        let max_w = width_d;
        let max_h = height_d;

        let tmp_h = max_h / h;

        let cropper_w = w * tmp_h;
        let cropper_h = h * tmp_h;

        if (cropper_h < 200) {
            cropper_h *= 200 / cropper_h;
            cropper_w *= 200 / cropper_h;
        } else if (cropper_w < 200) {
            cropper_h *= 200 / cropper_w;
            cropper_w *= 200 / cropper_w;
        }

        if (cropper_w < max_w) {
            // 縦の割合に縮小拡大したときに横幅が収まる場合
            $('.cropper-dialog .icon-preview').css('width', cropper_w);
            $('.cropper-dialog .icon-preview').css('height', cropper_h);
        } else {
            // 横の割合に縮小拡大する
            let tmp_w = max_w / w;
            cropper_w = w * tmp_w;
            cropper_h = h * tmp_w;
            $('.cropper-dialog .icon-preview').css('width', cropper_w);
            $('.cropper-dialog .icon-preview').css('height', cropper_h);
        }

        // 画像要素の追加
        let img = $('<img>', { id: 'cropper_image', src: file_path });
        $('.icon-preview').append(img);

        // js cropperを作成する
        $('#cropper_image').cropper({
            aspectRatio: 1 / 1,
            viewMode: 3,
            dragMode: 'move',
            cropBoxResizable: false,
        });
        $('.cropper-dialog').fadeIn(200);
        stopLoading();
    };
    cropper_image.src = file_path;
}

/**
 * 権限チェックボックスの値から権限IDを取得する
 * @param {*} canPost 
 * @param {*} canShare 
 */
function getAuthID(canPost, canShare) {
    if (canPost) {
        if (canShare) {
            return 4;
        } else {
            return 2;
        }
    } else {
        if (canShare) {
            return 3;
        } else {
            return 1;
        }
    }
}

/**
 * 参加方式を取得する
 * 1：自由参加
 * 2：承認
 * 3：パスワード
 * 4：パスワードと承認
 */
function getJoinType(joinApproval, joinPassword) {
    if (joinApproval) {
        return joinPassword ? 9 : 2;
    } else {
        return joinPassword ? 3 : 1;
    }
}

/**
 * 検索可否を取得する
 */
function getSearchType() {
    let radioValue = $('[name=radio-search-type]:checked').val();
    if (radioValue == 'possible') {
        return 1;
    } else {
        return 0;
    }
}

/**
 * デフォルト権限のチェックボックスのチェック状態を初期化します
 */
function initDefaultAuthCheckBox() {
    $('#checkbox-auth-1').prop('checked', false);
    $('#checkbox-auth-2').prop('checked', false);
    $('#checkbox-auth-3').prop('checked', false);

    switch (parseInt($('[name=default_auth_id]').val())) {
        case 1:
            break;
        case 2:
            $('#checkbox-auth-1').prop('checked', true);
            break;
        case 3:
            $('#checkbox-auth-2').prop('checked', true);
            break;
        case 4:
            $('#checkbox-auth-1').prop('checked', true);
            $('#checkbox-auth-2').prop('checked', true);
            break;
        case 5:
            $('#checkbox-auth-1').prop('checked', true);
            $('#checkbox-auth-2').prop('checked', true);
            $('#checkbox-auth-3').prop('checked', true);
            break;
    }
}

/**
 * 参加方式のラジオボタンを初期化します
 */
function initJoinTypeRadio(parent) {
    let type = $('[name=join_type]').val();
    if (type == '1') {
        $('#radio_password').prop('checked', true);
    } else if (type == '2') {
        $('#radio_permission').prop('checked', true);
    } else if (type == '9') {
        $('#radio_both').prop('checked', true);
    }
}

/**
 * 検索可否のラジオボタンを初期化します
 */
function initSearchTypeRadio() {
    if ($('[name=search_type]').val() == '1') {
        $('#radio_possible').prop('checked', true);
    } else {
        $('#radio_impossible').prop('checked', true);
    }
}

/**
 * input要素にlabelを関連付けている場合、label要素へのフォーカスイン、フォーカスアウトのイベントを設定する
 */
function setInputLabelEvents() {
    // フォーカスインでjqueryの余計なスタイルを解除してフォーカス用のスタイルを付与する
    $('input.input-label').on('focus', function (e) {
        let label = $(this).next();
        label.removeClass('ui-visual-focus ui-state-focus');
        label.addClass('label-focus');
    });
    // フォーカスアウトでスタイルを解除
    $('input.input-label').on('blur', function (e) {
        $(this).next().removeClass('label-focus');
    });
}

/**
 * 改行コードをhtmlの<br>タグに置換する
 * @param {*} str 
 */
function replaceNl2Br(str) {
    return str.replace(/\r\n|\r|\n/g, '<br>');
}

/**
 * クリップボードにテキストをコピーする
 * @param {*} textVal 
 */
function copyTextToClipboard(textVal) {
    // テキストエリアを用意する
    var copyFrom = document.createElement("textarea");
    // テキストエリアへ値をセット
    copyFrom.textContent = textVal;

    // bodyタグの要素を取得
    var bodyElm = document.getElementsByTagName("body")[0];
    // 子要素にテキストエリアを配置
    bodyElm.appendChild(copyFrom);

    // テキストエリアの値を選択
    copyFrom.select();
    // コピーコマンド発行
    var retVal = document.execCommand('copy');
    // 追加テキストエリアを削除
    bodyElm.removeChild(copyFrom);
    // 処理結果を返却
    return retVal;
}

/**
 * ページの先頭にスクロールする
 */
function scrollTop() {
    $('body,html').animate({ scrollTop: 0 }, 500);
}

/**
 * テキストボックスにエラーメッセージを表示する
 * @param {*} text 
 * @param {*} message 
 */
function setTextError(text, message) {
    text.parent().attr('data-message', message);
    text.parent().addClass('text-error');
}