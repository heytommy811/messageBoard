<?php

namespace App\modules\common;

use App\models\St_dgb;
use App\models\St_dgm;
use App\models\St_sks;
use App\modules\common\Constants;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\exceptions\MessageException;

/**
 * 伝言板共通クラス
 */
class BoardUtils
{
    /**
     * メンバ情報を取得する
     */
    public static function getMemberInfo($member_info)
    {
        $member = array();
        $member['user_id'] = $member_info['user_id'];   // ユーザーID
        $member['name'] = $member_info['name'];         // メンバー名
        $member['auth_id'] = $member_info['auth_id'];   // 権限
        $member['icon'] = IconUtil::getIconUrl($member_info['user_id']);

        return $member;
    }
}
