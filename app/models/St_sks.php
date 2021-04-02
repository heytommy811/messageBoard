<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\modules\common\IconUtil;

use App\exceptions\MessageException;

class St_sks extends Model
{
    // テーブル名
    protected $table = 'st_sks';
    // 主キー
    protected $primaryKey = 'join_request_id';
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['dgb_id', 'user_id', 'request_result'];

    /**
     * 参加申請を登録する
     */
    public function scopeAddRequest($query, $dgb_id, $user_id) {
        $query->create([
            'dgb_id' => $dgb_id,
            'user_id' => $user_id
        ]);
    }

    /**
     * 参加申請者一覧を取得する
     */
    public function scopeGetRequestUsers($query, $dgb_id) {
        // 申請中のユーザー一覧を取得する
        $requestUsers = $query->join('st_act', function ($join) {
            // アカウントを結合
            $join->on('st_sks.user_id', '=', 'st_act.id');
        })->where('st_sks.dgb_id', $dgb_id)->where('st_sks.request_result', 0)   // 申請結果が1：許可または2：拒否の場合は抽出しない
        ->select('st_sks.join_request_id', 'st_sks.user_id', 'st_act.account_name', 'st_sks.created_at')->orderBy('st_sks.join_request_id', 'asc')->get();

        foreach ($requestUsers as $requestUser) {
            $requestUser['icon'] = IconUtil::getIconUrl($requestUser['user_id']);
        }
        return $requestUsers;
    }

    /**
     * 参加申請を更新する
     */
    public function scopeUpdateRequestResult($query, $request) {

        $join_request_id = $request->input('join_request_id');
        $accept = $request->input('accept');
        $joinRequest = $query->where('join_request_id', $join_request_id)->first();
        if ($joinRequest == null) {
            Log::debug(sprintf('join_request_id dose not exists.[join_request_id:%s]', $join_request_id));
            throw new MessageException('無効な参加申請です。');
        }
        try {

            // トランザクションの開始
            DB::beginTransaction();
            $joinRequest->update(['request_result' => $accept]);
            // 申請許可の場合はメンバーに追加する
            if ($accept == 1) {
                $dgb_id = $joinRequest['dgb_id'];
                // 伝言板管理を検索してデフォルト権限を取得する
                $boardManage = St_dgk::getBoardManage($dgb_id);
                St_dgm::addMember($dgb_id, $joinRequest['user_id'], $boardManage['default_auth_id']);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * 伝言板に参加申請中であるか確認する
     */
    public function scopeHasRequest($query, $dgb_id, $user_id) {
        return $query->where('dgb_id', $dgb_id)->where('user_id', $user_id)->where('request_result', 0)->exists();
    }
}
