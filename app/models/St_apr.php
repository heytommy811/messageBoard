<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

use App\exceptions\MessageException;

class St_apr extends Model
{
    use SoftDeletes;
    // テーブル名
    protected $table = 'st_apr';
    // ソフトデリートカラム
    protected $dates = ['deleted_at'];
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['user_id', 'cert_id', 'url_yuko_kigen'];

    /**
     * パスワードリセットテーブルに登録する
     */
    public function scopeAddPasswordReset($usre_id, $cert_id) {
        $query->create([
            'user_id' => $usre_id,
            'cert_id' => $cert_id,
            'url_yuko_kigen' => Carbon::now()->addDay(),
        ]);
    }
    /**
     * 認証IDで有効期限内のレコードを検索する
     */
    public function scopeGetByCertId($query, $cert_id) {
        $row = $query->where('cert_id', $cert_id)->where('url_yuko_kigen', '>=', Carbon::now())->first();
        // 無効な認証IDの場合はエラー画面へ
        if ($row == null) {
            throw new MessageException('URLが無効です。', '/');
        }
        return $row;
    }
}
