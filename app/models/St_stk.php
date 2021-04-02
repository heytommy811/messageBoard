<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class St_stk extends Model
{
    use SoftDeletes;
    // テーブル名
    protected $table = 'st_stk';
    // ソフトデリートカラム
    protected $dates = ['deleted_at'];
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['dgn_id', 'comment_id', 'reply_user_id', 'user_id', 'stk_type'];

    /**
     * ユーザーの新着情報を取得する
     */
    public function scopeGetReplyInfomations($query, $user_id) {
        $stkSearchResults = $query->where('user_id', $user_id)
        ->select('dgn_id', 'stk_type', DB::raw('COUNT(stk_type) as count'), DB::raw('MAX(created_at) as created_at'))
        ->groupBy('dgn_id', 'stk_type')->orderBy('stk_type')->get();
        $infomations = [];
        foreach ($stkSearchResults as $stkSearchResult) {
            $messageSearchResult = St_dgn::where('dgn_id', $stkSearchResult['dgn_id'])->first();
            if ($messageSearchResult == null) continue;
            // 伝言のタイトルを取得する
            $message = $messageSearchResult['title'] . 'に'. $stkSearchResult['count'] . '件の%sがあります。';
            if ($stkSearchResult['stk_type'] == 1) {
                // コメントの場合
                $message = sprintf($message, 'コメント');
            } else {
                // いいねの場合
                $message = sprintf($message, 'いいね');
            }
            $info['message'] = $message;
            $infomations[] = [
                'date' => $stkSearchResult['created_at']->format('Y-m-d H:i'),
                'url' => 'message/' . $stkSearchResult['dgn_id'],
                'dgn_id' => $stkSearchResult['dgn_id'],
                'message' => $message,
            ];
        }
        return $infomations;
    }

    /**
     * コメントの新着情報を登録する
     */
    public function scopeAddCommentInfomation($query, $dgn_id, $comment_id, $user_id, $create_user_id) {
        $query->create([
            'dgn_id' => $dgn_id,
            'comment_id' => $comment_id,
            'reply_user_id' => $user_id,
            'user_id' => $create_user_id,
            'stk_type' => 1 // 1：コメント
        ]);
    }

    /**
     * いいねの新着情報を登録する
     */
    public function scopeAddLikeInfomation($query, $dgn_id, $user_id, $create_user_id) {
        $query->create([
            'dgn_id' => $dgn_id,
            'reply_user_id' => $user_id,
            'user_id' => $create_user_id,
            'stk_type' => 2 // 2：いいね
        ]);
    }
}
