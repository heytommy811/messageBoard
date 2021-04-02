<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

use App\exceptions\MessageException;

class St_dgm extends Model
{
    // テーブル名
    protected $table = 'st_dgm';
    // 主キー
    protected $primaryKey = ['dgb_id', 'user_id'];
    // increment無効化
    public $incrementing = false;
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['dgb_id', 'user_id', 'auth_id', 'name'];

    /**
     * 伝言板にメンバーを追加する
     */
    public function scopeAddMember($query, $dgb_id, $user_id, $auth_id, $name = null) {
        $params = [
            'dgb_id' => $dgb_id,
            'user_id' => $user_id,
            'auth_id' => $auth_id
        ];
        if (!empty($name)) {
            $params['name'] = $name;
        }
        $query->create($params);
    }

    /**
     * 伝言板にユーザが存在するか検証する
     */
    public function scopeValidateBoardMember($query, $dgb_id, $user_id) {
        if (!$this->isBoardMember($dgb_id, $user_id)) {
            Log::debug(sprintf('user does not member.[dgb_id:%s] [user_id:%s]', $dgb_id, $user_id));
            throw new MessageException('伝言板に参加していません。');
        }
    }

    /**
     * 伝言板メンバーかどうか
     */
    public function scopeIsBoardMember($query, $dgb_id, $user_id) {
        return $query->where('dgb_id', $dgb_id)->where('user_id', $user_id)->exists();
    }

    /**
     * 伝言板のメンバー情報を取得する
     */
    public function scopeGetMember($query, $dgb_id, $user_id) {
        $member = $query->where('dgb_id', $dgb_id)->where('user_id', $user_id)->first();
        if ($member === null) {
            Log::debug(sprintf('user does not member.[dgb_id:%s] [user_id:%s]', $dgb_id, $user_id));
            throw new MessageException('伝言板に参加していません。');
        }
        return $member;
    }

    /**
     * 伝言板のメンバー一覧を取得する
     */
    public function scopeGetMembers($query, $dgb_id) {
        // 追加順に取得することで先頭が作成者になる
        return $query->where('dgb_id', $dgb_id)->orderBy('created_at', 'asc')->get();
    }

    /**
     * 管理権限が存在するか検証する
     */
    public function scopeValidateManageAuth($query, $dgb_id, $user_id) {
        $member = $this->getMember($dgb_id, $user_id);
        if ($member['auth_id'] != 5) {
            Log::error(sprintf('insufficient auth_id. [auth_id:%s]', $member['auth_id']));
            throw new MessageException('権限が不足しています。');
        }
    }

    /**
     * 共有権限が存在するか検証する
     */
    public function scopeValidateShareAuth($query, $dgb_id, $user_id) {
        $member = $this->getMember($dgb_id, $user_id);
        if ($member['auth_id'] < 3) {
            Log::error(sprintf('insufficient auth_id. [auth_id:%s]', $member['auth_id']));
            throw new MessageException('権限が不足しています。');
        }
    }

    /**
     * 伝言板メンバーの権限を更新する
     */
    public function scopeUpdateMemberAuth($query, $request, $manage_user_id) {
        $dgb_id = $request->input('dgb_id');
        $target_user_id = $request->input('user_id');
        $update_to_auth_id = $request->input('auth_id');
        // ログインユーザーのメンバー情報を取得する
        $manageMember = $query->where('dgb_id', $dgb_id)->where('user_id', $manage_user_id)->first();
        if ($manageMember == null) {
            // ログインユーザーが伝言板のメンバーではない
            Log::error(sprintf('user does not member. [dgb_id:%s] [user_id:%s]', $dgn_id, $manage_user_id));
            throw new MessageException('伝言板メンバーではないため、変更できません。');
        }

        if ($manageMember['auth_id'] != 5) {
            // 権限IDが管理者以外の場合はエラー
            Log::error(sprintf('insufficient auth_id. [auth_id:%s]', $manageMember['auth_id']));
            throw new MessageException('権限が不足しています。');
        }

        // NOTE:　複合キーの場合はsave()で更新できない仕様
        $query->where('dgb_id', $dgb_id)->where('user_id', $target_user_id)->update(['auth_id' => $update_to_auth_id]);
    }

    /**
     * 伝言板参加時にメンバー名を設定（更新）する
     */
    public function scopeUpdateMemberName($query, $dgb_id, $user_id, $name) {
        $query->where('dgb_id', $dgb_id)->where('user_id', $user_id)->update(['name' => $name]);
    }
}
