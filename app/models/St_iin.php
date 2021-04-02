<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\models\St_dgn;
use App\models\St_stk;

class St_iin extends Model
{
    // テーブル名
    protected $table = 'st_iin';
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['dgn_id', 'user_id'];

    /**
     * いいねを登録する
     */
    public function scopeAddLike($query, $dgn_id, $user_id) {
        try {
            DB::beginTransaction();
            $query->create([
                'dgn_id' => $dgn_id,
                'user_id' => $user_id
            ]);
            // 伝言を検索
            $message = St_dgn::getMessage($dgn_id);
            if ($message['create_user_id'] != $user_id) {
                // 新着に登録する
                St_stk::addLikeInfomation($dgn_id, $user_id, $message['create_user_id']);
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * いいねを既にしているかどうか
     */
    public function scopeExistsLike($query, $dgn_id, $user_id) {
        return $query->where('dgn_id', $dgn_id)->where('user_id', $user_id)->exists();
    }

    /**
     * いいねを削除する
     */
    public function scopeDeleteLike($query, $dgn_id, $user_id) {
        $query->where('dgn_id', $dgn_id)->where('user_id', $user_id)->delete();
    }

    /**
     * いいねの件数を取得する
     */
    public function scopeGetLikeCount($query, $dgn_id) {
        return $query->where('dgn_id', $dgn_id)->count();
    }
}
