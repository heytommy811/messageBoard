<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

use App\models\St_cmt;
use App\models\St_iin;

use App\modules\common\ResponseUtils;

use App\exceptions\MessageException;

class St_dgn extends Model
{
    use SoftDeletes;
    // テーブル名
    protected $table = 'st_dgn';
    // 主キー
    protected $primaryKey = 'dgn_id';
    // ソフトデリートカラム
    protected $dates = ['deleted_at'];
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['dgb_id', 'title', 'message', 'create_user_id'];

    /**
     * 伝言を登録する
     */
    public function scopeAddMessage($query, $request, $user_id) {
        $query->create([
            'dgb_id' => $request->input('dgb_id'),
            'title' => $request->input('title'),
            'message' => $request->input('message'),
            'create_user_id' => $user_id,
        ]);
    }

    /**
     * 伝言板内のメッセージ一覧を取得する
     */
    public function scopeGetMessages($query, $dgb_id) {

        // 更新日時が新しい順に伝言の一覧を取得する
        $messages = $query->select(['dgn_id', 'title', 'message', 'updated_at'])->where('dgb_id', $dgb_id)->latest('updated_at')->get();
        foreach ($messages as $message) {
            $dgn_id = $message['dgn_id'];
            $message['last_update_dt'] = ResponseUtils::getUpdateTimeLabel($message['updated_at']);
            // コメントの件数を設定
            $count_comment = St_cmt::where('dgn_id', $dgn_id)->count();
            $message['count_comment'] = $count_comment;

            // いいねの件数を設定
            $count_iine = St_iin::where('dgn_id', $dgn_id)->count();
            $message['count_iine'] = $count_iine;
        }

        return $messages;
    }
    
    /**
     * メッセージを取得する
     */
    public function scopeGetMessage($query, $dgn_id) {
        $message = $query->where('dgn_id', $dgn_id)->first();
        if ($message == null) {
            // 有効な伝言板が存在しない場合
            Log::error(sprintf('message is invalid. [dgn_id:%s]', $dgn_id));
            throw new MessageException('伝言板が存在しません。');
        }

        return $message;
    }

    /**
     * メッセージ詳細を取得する
     */
    public function scopeGetMessageDetail($query, $dgn_id, $user_id) {
        $message = $this->getMessage($dgn_id);
        $message['last_update_dt'] = ResponseUtils::getUpdateTimeLabel($message['updated_at']);
        // いいねの件数を取得
        $message['count_iine'] = St_iin::where('dgn_id', $dgn_id)->count();
        // 自分自身がいいねしているかどうか
        $message['liked'] = St_iin::where('dgn_id', $dgn_id)->where('user_id', $user_id)->exists();

        return $message;
    }

    /**
     * メッセージを削除する
     */
    public function scopeDeleteMessage($query, $dgn_id, $user_id) {
        $message = $query->where('dgn_id', $dgn_id)->first();
        if ($message == null) {
            throw new MessageException('伝言が存在しません。');
        }
        $dgb_id = $message['dgb_id'];
        // 伝言板メンバーにユーザーが存在するかチェックする
        St_dgm::validateBoardMember($dgb_id, $user_id);

        // 削除可能な権限かチェックする
        // TODO: 管理者は可能とする
        if ($message['create_user_id'] != $user_id) {
            throw new MessageException('権限が不足しています。');
        }

        try {
            // トランザクションの開始
            DB::beginTransaction();
            // 伝言をソフトデリートする
            $message->delete();
            // コメントをソフトデリートする
            St_cmt::where('dgn_id', $dgn_id)->delete();
            // いいねをソフトデリートする
            St_iin::where('dgn_id', $dgn_id)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * メッセージを更新する
     */
    public function scopeUpdateMessage($query, $request, $user_id) {

        $dgn_id = $request->input('dgn_id');
        $update_title = $request->input('title');
        $update_message = $request->input('message');
        
        // 伝言を検索する
        $message = $this->where('dgn_id', $dgn_id)->first();
        if ($message == null) {
            throw new MessageException('伝言板が存在しません。');
        }
        $dgb_id = $message['dgb_id'];
        // 伝言板メンバーにユーザーが存在するかチェックする
        St_dgm::validateBoardMember($dgb_id, $user_id);

        // 更新可能な権限かチェックする
        // TODO: 管理者は可能とする
        if ($message['create_user_id'] != $user_id) {
            throw new MessageException('編集に失敗しました。');
        }
        
        try {
            // トランザクションの開始
            DB::beginTransaction();
            
            // 伝言を更新する
            $message->update([
                'title' => $update_title,
                'message' => $update_message,
            ]);

            // 伝言板を更新する
            St_dgb::updateBoard($dgb_id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
