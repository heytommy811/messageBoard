<?php

namespace App\modules\common;

use App\models\Sm_sys;
use App\modules\common\Constants;

/**
 * システム設定クラス
 */
class SystemSetting
{
    /**
     * 設定値を取得する
     */
    public static function getSettingValue($key) {
        return Sm_sys::where('key', $key)->first()['value'];
    }

    /**
     * システム設定情報をキーと値のペア配列を取得する
     */
    public static function getSettings() {
        $settings = Sm_sys::get();
        $results = array();
        foreach($settings as $setting) {
            $results[$setting['key']] = $setting['value'];
        }
        return $results;
    }

    /**
     * システム設定情報を取得する
     */
    public static function selectSystemSettings() {
        return Sm_sys::get();
    }

    /**
     * idで指定したレコードの設定値を更新します
     */
    public static function updateValueById($id, $value) {
        Sm_sys::where('id', $id)->update(['value' => $value]);
    }
}
