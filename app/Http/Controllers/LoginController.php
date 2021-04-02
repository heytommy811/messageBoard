<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\models\Sys_access;
use App\models\St_act;
use App\modules\common\ClientUtil;
use App\modules\common\Constants;
use App\modules\common\SystemSetting;
use App\modules\common\ResponseUtils;
use Carbon\Carbon;

use App\Http\Requests\LoginRequest;

use App\exceptions\MessageException;
use Exception;

class LoginController extends Controller
{
    /**
     * ログイン画面を表示
     */
    public function index(Request $request)
    {

        $user = $request->session()->get('users');
        $request->session()->forget('tmp_account_info');
        if ($user != null) {
            // ログイン中の場合はホーム画面にリダイレクト
            return redirect("home");
        }

        // アクセス情報を登録する
        Sys_access::addAccess();
        return view('page/login');
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        // セッションを削除する
        $request->session()->flush();
        // ログイン画面にリダイレクト
        return redirect("login")->with('message', 'ログアウトしました。');
    }

    /**
     * ログイン処理（post）
     */
    public function store(LoginRequest $request)
    {
        // パラメータを取得する
        $mail = $request->input('mail');
        $password = $request->input('password');
            
        // システム設定情報を取得する
        $sys_settings = SystemSetting::getSettings();
        
        // メールアドレスでアカウント情報を検索する
        $account_info = St_act::getByMail($mail);
        $this->loginValidater($account_info, $password, $sys_settings);
        try {

            // ログイン成功時は ログイン失敗をリセットして日時を更新する
            $account_info->login_fail_cnt = 0;
            $account_info->login_fail_dt = null;
            $account_info->login_lock_cnt = 0;
            $account_info->login_lock_dt = null;
            $account_info->last_login_dt = Carbon::now();
            $account_info->save();

            // ログインユーザー情報を生成する
            $user = $this->createUsers($account_info);
        
            // ユーザー情報をセッションに保存する
            $request->session()->regenerate();
            $request->session()->put('users', $user);
            Log::info(sprintf('Login success. [id:%s]', $user[Constants::KEY_ID]));

            // 管理者の場合
            if ($user[Constants::KEY_ADMIN] == 1) {
                return redirect('admin');
            }

        } catch (Exception $e) {
            Log::error($e->getTraceAsString());
            return redirect("error");
        }
        return redirect('home');
    }

    // ホーム画面表示（get）
    public function home(Request $request)
    {
        try {

            // ログイン状態（セッションが有効の場合）
            $user = $request->session()->get('users');
            if ($user == null) {
                Log::debug('sesstion timeout.');
                return redirect("login")->with('message', Constants::MESSAGE_SESSION_TIME_OUT);
            }

            $data = array();
            // ヘッダー情報を設定する
            ResponseUtils::setHeaders($data, $user);

            // 共有URLを実行していた場合
            $invite_id = $request->session()->get('invite_id');
            if (!empty($invite_id)) {
                $data['invite_id'] = $invite_id;
                $request->session()->forget('invite_id');
            }
            
        } catch (Exception $e) {
            Log::error($e);
            return redirect("error");
        }
        return view('page/top', $data);
    }

    /**
     * ログイン認証を行う
     */
    private function loginValidater($account_info, $password, $sys_settings)
    {
        $message_login_fail = $sys_settings['message_login_fail'];
        $messsage_login_lock = $sys_settings['messsage_login_lock'];
        $message_account_lock = $sys_settings['message_account_lock'];
        $limit_login_fail_count = $sys_settings['limit_login_fail_count'];
        $limit_login_lock_count = $sys_settings['limit_login_lock_count'];
        $limit_login_fail_minutes = $sys_settings['limit_login_fail_minutes'];
        $limit_login_lock_minutes = $sys_settings['limit_login_lock_minutes'];

        // アカウントロック中
        if ($account_info->account_lock == '1' ) {
            return $message_account_lock;
        }

        // ログインロック中の場合
        $limit = Carbon::now()->subMinutes(60);
        if ($account_info->login_lock_dt != null &&   $limit->lt($account_info->login_lock_dt)) {
            return $messsage_login_lock;
        }

        // ハッシュチェック
        if (!Hash::check($password, $account_info->password)) {
            // パスワード不一致
            $fail_limit = Carbon::now()->subMinutes(60);
            if ($account_info->login_fail_dt != null && $fail_limit->lt($account_info->login_fail_dt)) {
                // 一定時間内に失敗した場合
                if ($limit_login_fail_count <= $account_info->login_fail_cnt) {
                    // ログイン失敗をリセット
                    $account_info->login_fail_cnt = 0;
                    $account_info->login_fail_dt = null;

                    // アカウント凍結
                    if ($limit_login_lock_count <= $account_info->login_lock_cnt) {
                        $account_info->account_lock = 1;
                        $account_info->login_lock_cnt = 0;
                        $account_info->login_lock_dt = null;
                        return $message_account_lock;
                    } else {
                        // ログインロックする
                        $account_info->login_lock_cnt = $account_info->login_lock_cnt + 1;
                        $account_info->login_lock_dt = Carbon::now();
                        return $messsage_login_lock;
                    }
                } else {
                    // ログイン失敗をカウントアップ
                    $account_info->login_fail_cnt = ($account_info->login_fail_cnt + 1);
                    $account_info->login_fail_dt = Carbon::now();
                }
            } else {
                // 新たにカウントする
                $account_info->login_fail_cnt = 1;
                $account_info->login_fail_dt = Carbon::now();
            }
            $account_info->save();
            throw new MessageException($message_login_fail, 'login');
            // return $message_login_fail;
        }
    }

    /**
     * ログインユーザー情報を作成する
     */
    private function createUsers($account_info)
    {
        $user[Constants::KEY_ID] = $account_info['id'];
        $user[Constants::KEY_MAIL] = $account_info['mail'];
        $user[Constants::KEY_ACCOUNT_NAME] = $account_info['account_name'];
        $user[Constants::KEY_ADMIN] = $account_info['admin'];
        return $user;
    }

}
