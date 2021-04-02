<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\modules\common\ClientUtil;
use Carbon\Carbon;

class Sys_access extends Model
{
    // テーブル名
    protected $table = 'sys_access';
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['ip', 'agent', 'is_pc'];

    /**
     * アクセス情報を登録する
     */
    public function scopeAddAccess($query) {
        $ci = ClientUtil::getClientInfo();
        $query->create([
            'ip' => $ci['ip'],
            'agent' => $ci['user_agent'],
            'is_pc' => $ci['is_pc'],
        ]);
    }
}
