<?php

namespace App\models;

use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\modules\common\ClientUtil;

use App\exceptions\MessageException;

use Carbon\Carbon;

class St_acm extends Model
{
    use SoftDeletes;
    // テーブル名
    protected $table = 'st_acm';
    // ソフトデリートカラム
    protected $dates = ['deleted_at'];
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['mail', 'password', 'account_name', 'ip', 'url_yuko_kigen', 'url', 'cert_id'];

    /**
     * アカウント申し込み情報を登録する
     */
    public function scopeAddAccountRequest($query, $mail, $password, $name, $url, $cert_id) {
        $query->create([
            'mail' => $mail,
            'password' => Hash::make($password),
            'account_name' => $name,
            'ip' => ClientUtil::getClientInfo()['ip'],
            'url_yuko_kigen' => Carbon::now()->addDay(),
            'url' => $url,
            'cert_id' => $cert_id
        ]);
    }

    /**
     * メールアドレスを検証する
     */
    public function scopeValidateMail($query, $mail) {
        //　既に申込済みのアドレスの場合
        if ($query->where('mail', $mail)->where('url_yuko_kigen', '>=', Carbon::now())->exists()) {
            throw new MessageException('既に申込されているメールアドレスです。');
        }
    }

    /**
     * アカウント申し込み情報を取得する
     */
    public function scopeGetAccountRequest($query, $cert_id) {
        // アカウント申込を検索する
        $accountRequestSearchResult = $query->where('cert_id', $cert_id)->first();

        // 既に登録した場合、ソフトデリートされているため抽出されない
        if ($accountRequestSearchResult == null) {
            Log::info(sprintf('cert_id does not exists.[cert_id:%s]', $cert_id));
            throw new MessageException('URLが無効です。', '/');
        }
        // 有効期限をチェックする
        $limit = $accountRequestSearchResult['url_yuko_kigen'];
        if (Carbon::now()->gt($limit)) {
            Log::info(sprintf('url has expired.[url_yuko_kigen:%s]', $limit));
            return redirect("/")->with('message', '');
            throw new MessageException('有効期限を過ぎました。再度お手続きを行ってください。', '/');
        }

        // 既に登録済みのメールアドレスの場合エラー
        if (St_act::where('mail', $accountRequestSearchResult['mail'])->exists()) {
            Log::error(sprintf('mail has already exists.[mail:%s]', $accountRequestSearchResult['mail']));
            throw new MessageException('URLが無効です。', '/');
        }
        return $accountRequestSearchResult;
    }

    /**
     * アカウント申し込み情報を削除する
     */
    public function scopeDeleteAccountRequest($query, $cert_id) {
        $query->where('cert_id', $cert_id)->delete();
    }
}
