<?php

namespace App\modules\common;

use App\modules\common\Constants;
use App\modules\common\IconUtil;

use Carbon\Carbon;

/**
 * レスポンス共通クラス
 */
class ResponseUtils
{
    /**
     * アカウント情報を元にヘッダー情報を設定する
     */
    public static function setHeaders(&$data, $users)
    {
        $data['account_name'] = $users[Constants::KEY_ACCOUNT_NAME];
        $data['account_mail'] = $users[Constants::KEY_MAIL];
        $icon_path = sprintf(Constants::ICON_FILE_PATH, $users[Constants::KEY_ID]);
        $data['account_icon'] = file_exists($icon_path) ? IconUtil::getBase64png($icon_path) : IconUtil::getBase64png(sprintf(Constants::ICON_FILE_PATH, 'default'));
    }

    /**
     * 現在時刻からの差分を表示用に取得する
     */
    public static function getUpdateTimeLabel($time) {
        $diff = $time->diff(Carbon::now());
        if (0 < $diff->y) {
            return $diff->y . '年前';    // 年
        } else if (0 < $diff->m) {
            return $diff->m . '月前';    // 月
        } else if (0 < $diff->d) {
            return $diff->d . '日前';    // 日
        } else if (0 < $diff->h) {
            return $diff->h . '時間前';    // 時
        } else if (0 < $diff->i) {
            return $diff->i . '分前';    // 分
        } else if (0 < $diff->s) {
            return $diff->s . '秒前';    // 秒
        } else {
            return '0秒前';
        }
    }
}
