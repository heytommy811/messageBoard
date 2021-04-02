<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

use Exception;
use App\exceptions\MessageException;

class St_dgs extends Model
{
    use SoftDeletes;
    // テーブル名
    protected $table = 'st_dgs';
    // 主キー
    protected $primaryKey = 'dgb_invite_id';
    // ソフトデリートカラム
    protected $dates = ['deleted_at'];
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['dgb_id', 'invite_id', 'create_user_id', 'invite_yuko_kigen'];

    /**
     * 招待情報を登録する
     */
    public function scopeAddInvite($query, $dgb_id, $invite_id, $user_id) {
        $query->create([
            'dgb_id' => $dgb_id,
            'invite_id' => $invite_id,
            'create_user_id' => $user_id,
            'invite_yuko_kigen' => Carbon::now()->addDay(),
        ]);
    }

    /**
     * 招待情報を取得する
     */
    public function scopeGetInvite($query, $invite_id) {
        // 存在しない招待URL
        $invite = $query->where('invite_id', $invite_id)->first();
        if ($invite == null) {
            Log::error(sprintf('invalid invite_id.[invite_id:%s]', $invite_id));
            throw new MessageException('不正な共有URLです。', 'home');
        }

        // 有効期限をチェックする
        $limit = $invite['invite_yuko_kigen'];
        if (Carbon::now()->gt($limit)) {
            Log::info(sprintf('share url has expired.[invite_yuko_kigen:%s]', $limit));
            throw new MessageException('共有URLの有効期限が過ぎています。', 'home');
        }

        return $invite;
    }

}
