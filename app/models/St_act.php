<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\models\St_apr;

use App\exceptions\MessageException;

/**
 * アカウント（st_act）テーブルのモデルクラス
 */
class St_act extends Model
{
    use SoftDeletes;
    // テーブル名
    protected $table = 'st_act';
    // ソフトデリートカラム
    protected $dates = ['deleted_at'];
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['mail', 'password', 'account_name'];

    /**
     * メールアドレスからユーザーを検索する
     */
    public function scopeGetByMail($query, $mail) {
        if ($query->where('mail', $mail)->exists()) {
            return $query->where('mail', $mail)->first();
        }
        return '';  // nullを返却できないので空文字を返す
    }

    /**
     * アカウント申し込み情報を登録する
     */
    public function scopeAddAccount($query, $accountRequest) {
        $account = $query->create([
            'mail' => $accountRequest['mail'],
            'password' => $accountRequest['password'],
            'account_name' => $accountRequest['account_name']
        ]);
        return $account['id'];
    }

    /**
     * アカウント名を更新する
     */
    public function scopeUpdateAccountNameById($query, $user_id, $account_name) {
        $query->find($user_id)->update(['account_name' => $account_name]);
    }

    /**
     * パスワードを更新する
     */
    public function scopeUpdatePasswordById($query, $user_id, $request) {
        $result = [];
        $password_before = $request->input('password_before');
        $password_after = $request->input('password_after');
        $account = $query->find($user_id);
        if (Hash::check($password_before, $account->password)) {
            if (empty($password_after)) {
                $result = ["変更後のパスワードが未入力です。"];
            } else {
                $account->password = Hash::make($password_after);
                $account->save();
            }
        } else {
            $result = ["変更前のパスワードが正しくありません。"];
        }
        return $result;
    }
    
    /**
     * パスワードを更新する
     */
    public function scopeResetPasswordById($query, $user_id, $request) {
        $result = [];
        $password_before = $request->input('password_before');
        $password_after = $request->input('password_after');
        $account = $query->find($user_id);
        if (Hash::check($password_before, $account->password)) {
            if (empty($password_after)) {
                $result = ["変更後のパスワードが未入力です。"];
            } else {
                $account->password = Hash::make($password_after);
                $account->save();
            }
        } else {
            $result = ["変更前のパスワードが正しくありません。"];
        }
        return $result;
    }

    /**
     * メールアドレスを検証する
     */
    public function scopeValidateMail($query, $mail) {
        // 既に登録済みのアドレスの場合
        if ($query->where('mail', $mail)->exists()) {
            throw new MessageException('既に登録されているメールアドレスです。');
        }
    }

    /**
     * パスワードをリセットする
     */
    public function scopeResetPassword($query, $request) {
        $cert_id = $request->input('cert_id');
        $password = $request->input('password');

        try {
            // トランザクションの開始
            DB::beginTransaction();
            // パスワードリセットテーブルを検索する
            $row = St_apr::getByCertId($cert_id);
            $user_id = $row['user_id'];
            if ($query->find($user_id)->exists()) {
                Log::debug('user does not exists.[user_id:'.$user_id.']');
                throw new MessageException('ユーザーが存在しません。');
            }

            $query->find($user_id)->update(['password' => Hash::make($password)]);
            // パスワードリセットをソフトデリートする
            $row->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
