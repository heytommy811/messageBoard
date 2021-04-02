/* manage.js */
$(function() {
    $(document).on('click', '.system-settings ul li button', function() {
        let li = $(this).parents('li');
        let id = li.find('.id').text();
        let value = li.find('.value input').val();
        let data = {
            id: id,
            value: value,
        }
        startLoading();
        getResponse('admin/systemSetting', data).done(function() {

        }).always(function() {
            stopLoading();
        });
    });

    $(document).on('click', '.account-request ul li button', function() {
        let li = $(this).parents('li');
        let id = li.find('[name=id]').val();
        let data = {
            id: id,
        }
        startLoading();
        getResponse('admin/registryAccountRequest', data).done(function() {
            location.reload();
        }).always(function() {
            stopLoading();
        });
    });

    // ユーザー検索ボタン押下時のイベント
    $('.user .search-keyword button').click(function() {

        searchUsers();
    });

    // ロック解除ボタン押下時のイベント
    $('.user-search-result').on('click', 'button', function() {
        let parents = $(this).parents('tr');
        let id = parents.find('.id').text();
        let data = {
            id: id,
        }
        startLoading();
        getResponse('admin/resetAccountLock', data).done(function() {
            searchUsers();
        }).fail(function() {
            stopLoading();
        });
    });

    function searchUsers() {
        let keyword = $('[name=keyword').val();
        // if (!keyword) {
        //     return;
        // }

        let data = {
            keyword: keyword,
        }
        startLoading();
        getResponse('admin/searchUser', data).done(function(response) {
            
            stopLoading();
            if (response.user_list && 0 < response.user_list.length) {
                $('.user-search-result').css('display', 'block');
                $('.error-message').css('display', 'none');
                $('.user-search-result tbody').empty();
                // 検索結果を表示する
                setUserSearhResult(response.user_list);
            } else {
                $('.error-message').css('display', 'block');
                $('.user-search-result').css('display', 'none');
            }
        });
    }

    
    function setUserSearhResult(user_list) {
        $.each(user_list, function(i, user) {
            let tr = $('<tr>');
            tr.append($('<td>', { class: "id", text: user.id }));
            tr.append($('<td>', { class: "", text: user.mail }));
            tr.append($('<td>', { class: "", text: user.account_name }));
            tr.append($('<td>', { class: "", text: user.created_at }));
            tr.append($('<td>', { class: "", text: user.last_login_dt }));
            tr.append($('<td>', { class: "", text: user.login_fail_cnt }));
            tr.append($('<td>', { class: "", text: user.login_fail_dt }));
            tr.append($('<td>', { class: "", text: user.login_lock_cnt }));
            tr.append($('<td>', { class: "", text: user.login_lock_dt }));
            tr.append($('<td>', { class: "", text: user.account_lock }));
            tr.append($('<td>', { class: ""}).append($('<button>', { text: "解除" })));
            $('.user-search-result tbody').append(tr);
        });
    }
});