<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

use App\modules\common\IconUtil;
use App\modules\common\ResponseUtils;

use App\models\St_stk;

class St_cmt extends Model
{
    use SoftDeletes;
    // テーブル名
    protected $table = 'st_cmt';
    // 主キー
    protected $primaryKey = 'comment_id';
    // ソフトデリートカラム
    protected $dates = ['deleted_at'];
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['dgn_id', 'comment', 'post_user_id'];

    /**
     * コメントを登録する
     */
    public function scopeAddComment($query, $dgn_id, $comment, $user_id, $message_create_user_id) {

        try {
            DB::beginTransaction();
        
            $comment = $query->create([
                'dgn_id' => $dgn_id,
                'comment' => $comment,
                'post_user_id' => $user_id
            ]);
            
            // 自分のメッセージへのコメントは新着に追加しない
            if ($message_create_user_id != $user_id) {
                // 新着を登録する
                St_stk::addCommentInfomation($dgn_id, $comment['comment_id'], $user_id, $message_create_user_id);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * コメントの件数を取得する
     */
    public function scopeGetComments($query, $dgb_id, $dgn_id, $login_user_id) {
        $comment_search_results = $query->join('st_dgm', function ($join) use (&$dgb_id) {
            $join->on('st_cmt.post_user_id', '=', 'st_dgm.user_id')
            ->where('st_dgm.dgb_id', $dgb_id);
        })->where('st_cmt.dgn_id', $dgn_id)->select('st_cmt.comment_id', 'st_dgm.name', 'st_cmt.comment', 'st_cmt.updated_at', 'st_cmt.post_user_id')->get();
        $comment_list = array();
        foreach ($comment_search_results as $comment_search_result) {
            $comment = array();
            $comment['cmt_id'] = $comment_search_result['comment_id'];
            $comment['icon'] =IconUtil::getIconUrl($comment_search_result['post_user_id']);
            $comment['name'] = $comment_search_result['name'];
            $comment['post_dt'] = ResponseUtils::getUpdateTimeLabel($comment_search_result['updated_at']);
            $comment['comment'] = $comment_search_result['comment'];
            $comment['can_delete'] = $comment_search_result['post_user_id'] == $login_user_id;
            array_push($comment_list, $comment);
        }
        return $comment_list;
    }

    /**
     * コメントを削除する
     */
    public function scopeDeleteComment($query, $comment_id) {
        try {
            DB::beginTransaction();
            
            // コメントを削除する（ソフトデリート）
            $query->where('comment_id', $comment_id)->delete();
            // 新着を削除する
            St_stk::where('comment_id', $comment_id)->delete();

            DB::commit();
            
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
