<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

use App\exceptions\MessageException;

class St_dgk extends Model
{
    use SoftDeletes;
    //
    protected $table = 'st_dgk';
    protected $primaryKey = 'dgb_id';
    // ソフトデリートカラム
    protected $dates = ['deleted_at'];
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['dgb_id', 'default_auth_id', 'join_type', 'search_type', 'join_password'];

    /**
     * 伝言板管理を登録する
     */
    public function scopeAddBoardManage($query, $request, $dgb_id) {

        $join_type = $request->input('join_type');
        $params = [
            'dgb_id' => $dgb_id,
            'default_auth_id' => $request->input('default_auth_id'),
            'join_type' => $join_type,
            'search_type' => $request->input('search_type')
        ];
        // 3:パスワードのみ、9:パスワードと作成者による承認
        if ($join_type == '3' || $join_type == '9') {
            $params['join_password'] = $request->input('join_password');
        }
        $query->create($params);
    }

    /**
     * 伝言板の管理情報を取得する
     */
    public function scopeGetBoardManage($query, $dgb_id) {
        $manage = $query->where('dgb_id', $dgb_id)->first();
        if ($manage == null) {
            Log::error(sprintf('board manage does not existd. [dgb_id:%s]', $dgb_id));
            throw new MessageException('伝言板がありません。');
        }

        return $manage;
    }

    /**
     * 伝言板管理情報を更新する
     */
    public function scopeUpdateManage($query, $request) {
        $manage = $this->getBoardManage($request->input('dgb_id'))->update([
            'default_auth_id' => $request->input('default_auth_id'),
            'join_type' => $request->input('join_type'),
            'search_type' => $request->input('search_type'),
            'join_password' => ($request->input('join_type') == 3 || $request->input('join_type') == 9) ? $request->input('join_password') : ''
        ]);
    }
}
