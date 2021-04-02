/* login.js */
$(function () {

	// アカウント作成のセットアップ
	setUpCreateAccount(); 
	// パスワードリセットのセットアップ
	setUpResetPassword(); 

	if ($('.error-message')) {
		$('.error-message').css('display', 'block');
	}

	// emailの入力イベントで未入力チェックを行う
	$('[name=mail],[name=password]').on('input', function() {
		if ($(this).val()) {
			if (!$(this).hasClass('valid')) {
				// 入力済みの場合はvalidをクラスに追加する
				$(this).addClass('valid');
			}
		} else {
			$(this).removeClass('valid');
		}
	});

	// ログインボタン押下時
	$('#button_login').on('click', function () {
		login();
		return false;
	});

	// パスワードテキストボックスでEnter押下した場合もログイン
	$('[name=password]').on('keypress', function (e) {
		if (e.key === 'Enter') {
			login();
			return false;
		}
	});


	/**
	 * ログイン処理
	 */
	function login() {
		if (isValidateError()) {
			return false;
		}
		formSubmit();
	}

	/**
	 * 入力値検証
	 */
	function isValidateError() {
		
		let result = false;

		// メールアドレスの入力値チェック
		if (!$('[name=mail]').val()) {
			setTextError($('[name=mail]'), 'メールアドレスを入力してください');
			result = true;
		}

		// パスワードの入力値チェック
		if (!$('[name=password]').val()) {
			setTextError($('[name=password]'), 'パスワードを入力してください');
			result = true;
		}

		return result;
	}
});
