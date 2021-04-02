<?php

namespace App\modules\common;

/**
 * 定数クラス
 */
class Constants {
    
    /**
     * ユーザー情報のアカウント名のキー
     */
    const KEY_ID = 'id';
    /**
     * ユーザー情報のアカウント名のキー
     */
    const KEY_MAIL = 'mail';
    /**
     * ユーザー情報のアカウント名のキー
     */
    const KEY_ACCOUNT_NAME = 'name';
    /**
     * ユーザー情報の管理者フラグのキー
     */
    const KEY_ADMIN = 'admin';
    /**
     * アイコン画像パス
     * %sにはアカウントIDを埋め込む
     */
    const ICON_FILE_PATH = 'storage/app/img/icon/%s.png';

    /**
     * アイコン画像を格納するディレクトリ
     */
    const ICON_PATH = 'img/icon';

    /**
     * 登録前のアイコン画像を格納するディレクトリ
     */
    const ICON_TEMP_PATH = 'img/icon_tmp';
    /**
     * セッションタイムアウト時のメッセージ
     */
    const MESSAGE_SESSION_TIME_OUT = 'セッションがタイムアウトしました。再度ログインして下さい。';

}