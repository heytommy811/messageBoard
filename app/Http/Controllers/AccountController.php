<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\UpdateProfileRequest;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\models\St_act;
use App\models\St_acm;
use App\models\St_apr;
use App\models\Sys_access;
use App\modules\common\Constants;
use App\modules\common\IconUtil;
use App\modules\common\MailUtil;
use App\modules\common\ResponseUtils;
use App\modules\common\SystemSetting;
use Carbon\Carbon;

use Exception;
use App\exceptions\MessageException;
use App\Exceptions\TimeoutException;

class AccountController extends Controller
{
    /**
     * メールを送信してアカウントの作成処理を行う（仮登録）
     */
    public function store(Request $request)
    {
        
        $name = $request->input('account_name');
        $mail = $request->input('account_mail');
        $password = $request->input('account_password');

        // メールアドレスを検証する
        St_act::validateMail($mail);
        St_acm::validateMail($mail);

        if (strlen($name) < 1) {
            throw new MessageException('アカウント名が未入力です。');
        }
        if (strlen($password) < 2) {
            throw new MessageException('パスワードが短すぎます。');
        } 

        // アイコン画像の保存
        $file_name = IconUtil::saveCroppingIcon($request);

        // 認証キーを発行
        $cert_id = uniqid();
        if (!empty($file_name)) {
            // アイコン画像を設定する
            IconUtil::renameTempIcon($file_name, $cert_id);
        }

        try {
            $url = url("account/complete/{$cert_id}");
            DB::beginTransaction();
            St_acm::addAccountRequest($mail, $password, $name, $url, $cert_id);
            $mail_mode = SystemSetting::getSettingValue('mail_mode');
            // メールモードが有効の場合
            if ($mail_mode == '1') {
                
                // メール本文を作成する
                $message = MailUtil::createMailMessage(
                    SystemSetting::getSettingValue('entry_mail_message_template_before'),
                    $url,
                    SystemSetting::getSettingValue('entry_mail_message_template_after')
                );

                try {
                    // メール送信処理
                    MailUtil::sendMail(
                        SystemSetting::getSettingValue('entry_mail_from'),
                        $mail,
                        SystemSetting::getSettingValue('entry_mail_subject'),
                        $message
                    );
                } catch (Exception $e) {
                    Log::error($e);
                    throw new MessageException('メール送信に失敗しました。');
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);
            throw $e;
        }
        return response()->json(['status' => 'success']);
    }

    /**
     * アカウント作成完了画面を表示する
     */
    public function complete(Request $request, $cert_id)
    {
        
        // アカウント申込を検索する
        $accountRequestSearchResult = St_acm::getAccountRequest($cert_id);
        
        try {
            // アカウントを作成する
            DB::beginTransaction();
            $id = St_act::addAccount($accountRequestSearchResult);
            // アカウント申込を削除する
            St_acm::deleteAccountRequest($cert_id);
            DB::commit();
            
            // アイコン画像を設定する
            IconUtil::settingIcon($cert_id.'.png', $id);
            
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);
            return redirect("error");
        }

        return redirect("/")->with('message', 'アカウントの作成が完了しました。');
    }

    /**
     * アカウント情報を更新する
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        
        // ログイン状態（セッションが有効の場合）
        $user = $this->getUserSession($request);
        
        // アイコン画像の保存
        $file_name = IconUtil::saveCroppingIcon($request);
        if (!empty($file_name)) {
            // アイコン画像を設定する
            IconUtil::settingIcon($file_name, $user[Constants::KEY_ID]);
            $response['src'] = IconUtil::getBase64png(IconUtil::getIconFilePath($user[Constants::KEY_ID] . '.png'));
        }
        // アカウントテーブルを更新する
        $account_name = $request->input('account_name');
        St_act::updateAccountNameById($user[Constants::KEY_ID], $account_name);
        // セッションのユーザー情報を更新する
        $user[Constants::KEY_ACCOUNT_NAME] = $account_name;
        $request->session()->put('users', $user);
            
        return response()->json(['status' => 'success']);
    }

    /**
     * パスワードを変更する
     */
    public function updatePassword(Request $request)
    {
        // ログイン状態（セッションが有効の場合）
        $user = $this->getUserSession($request);
        // アカウントテーブルを更新する
        return response()->json([
            'status' => 'success',
            'validateError' => St_act::updatePasswordById($user[Constants::KEY_ID], $request)
        ]);
    }

    /**
     * パスワードリセットの案内メールを送信する
     */
    public function sendPasswordResetMail(Request $request)
    {
        
        $mail = $request->input('mail');
        
        // ユーザー情報を検索する
        $user_info = St_act::getByMail($mail);
        
        // 登録されたメールアドレス以外の場合はエラー
        if ($user_info == null) {
            Log::debug('no user: '. $mail);
            throw new MessageException('登録されていないメールアドレスです。');
        }
        
        // 認証IDを発行
        $cert_id = uniqid();
        
        try {
            DB::beginTransaction();
            
            // パスワードリセットテーブルに登録する
            St_apr::addPasswordReset($user_info['id'], $cert_id);

            // メールモードが有効の場合案内メールを送信する
            if (SystemSetting::getSettingValue('mail_mode') == '1') {
                // メール本文を作成する
                $message = MailUtil::createMailMessage(
                    SystemSetting::getSettingValue('reset_password_mail_message_template_before'),
                    url("account/password/reset/{$cert_id}"),
                    SystemSetting::getSettingValue('reset_password_mail_message_template_after')
                );

                try {
                    // メール送信処理
                    MailUtil::sendMail(
                        SystemSetting::getSettingValue('reset_password_mail_from'),
                        $mail,
                        SystemSetting::getSettingValue('reset_password_mail_subject'),
                        $message
                    );
                } catch (Exception $e) {
                    Log::error($e);
                    throw new MessageException('メール送信に失敗しました。');
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e);
            throw $e;
        }
        return response()->json(['status' => 'success']);
    }

    /**
     * パスワードリセットのページを表示します
     */
    public function resetPasswordPage(Request $request, $cert_id)
    {
        // パスワードリセットテーブルを検索する
        $row = St_apr::getByCertId($cert_id);

        // アクセス情報を登録する
        Sys_access::addAccess();

        $data['cert_id'] = $row['cert_id'];
        return view('page/resetPassword', $data);
    }

    /**
     * パスワードをリセットする
     */
    public function resetPasssword(Request $request)
    {
        St_act::resetPassword($request);
        return response()->json(['status' => 'success']);
    }
}
