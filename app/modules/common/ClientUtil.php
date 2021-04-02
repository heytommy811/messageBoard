<?php

namespace App\modules\common;

use App\modules\common\Constants;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * クライアント共通クラス
 */
class ClientUtil
{
    /**
     * 接続元のクライアント情報を取得する
     */
    public static function getClientInfo() {
        return [
            'ip' => \Request::ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'is_pc' => self::isPc(),
        ];
    }

    /**
     * 接続元のクライアントがPCかどうか判定する
     */
    public static function isPc() {
        return !preg_match('/(iPhone|Android.*Mobile|Windows.*Phone)/', $_SERVER['HTTP_USER_AGENT']);
    }
}
