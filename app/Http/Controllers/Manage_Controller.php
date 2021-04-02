<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\models\St_act;
use App\models\St_acm;
use App\modules\common\Constants;
use App\modules\common\SystemSetting;
use App\modules\common\IconUtil;
use Carbon\Carbon;

use Exception;

/**
 * 管理画面コントローラー
 */
class Manage_Controller extends Controller
{
    /**
     * 管理者画面を開く
     */
    public function admin(Request $request) {

        // ログイン状態（セッションが有効の場合）
        $users = $request->session()->get('users');
        if ($users == null) {
            Log::debug('sesstion timeout.');
            return redirect("/")->with('message', Constants::MESSAGE_SESSION_TIME_OUT);
        }


        if ($users[Constants::KEY_ADMIN] == 1) {
            return view('page/manage', $this->getManageData());
        }
        return redirect('home');
    }

    /**
     * 管理画面に表示するデータを取得する
     */
    private function getManageData() {
        $sys_settings = SystemSetting::selectSystemSettings();
        $data = array();
        $data['system_settings'] = $sys_settings;
        $data['account_request'] = $this->selectAccountRequest();
        return $data;
    }

    /**
     * アカウント申込検索
     */
    private function selectAccountRequest() {

        // 有効期限関係なしに抽出する
        $accountRequestSearchResults = St_acm::get();

        $account_request = array();
        foreach ($accountRequestSearchResults as $accountRequestSearchResult) {
            $request = array();
            $request['id'] = $accountRequestSearchResult['id'];
            $request['mail'] = $accountRequestSearchResult['mail'];
            $request['name'] = $accountRequestSearchResult['account_name'];
            $request['date'] = $accountRequestSearchResult['url_yuko_kigen'];
            array_push($account_request, $request);
        }
        return $account_request;
    }

    /**
     * システム設定情報を更新する
     */
    public function systemSetting(Request $request)
    {
        try {

            // ログイン状態（セッションが有効の場合）
            $users = $request->session()->get('users');
            if ($users == null) {
                Log::debug('sesstion timeout.');
                $response['message'] = 'セッションがタイムアウトしました。';
                throw new Exception();
            }

            $id = $request->input('id');
            $value = $request->input('value');

            SystemSetting::updateValueById($id, $value);

            $response['status'] = 'success';
            
        } catch (Exception $e) {
            $response['status'] = 'error';
        }
        return response()->json($response);
    }

    /**
     * ユーザー情報を検索する
     */
    public function searchUser(Request $request)
    {
        try {

            // ログイン状態（セッションが有効の場合）
            $users = $request->session()->get('users');
            if ($users == null) {
                Log::debug('sesstion timeout.');
                $response['message'] = 'セッションがタイムアウトしました。';
                throw new Exception();
            }

            $keyword = $request->input('keyword');

            if (empty($keyword)) {
                $accountSearchResult = St_act::get();
            } else {
                $accountSearchResult = St_act::where(function($query) use ($keyword) {
                    $query->orWhere('id', $keyword)
                    ->orWhere('mail', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('account_name', 'LIKE', '%' . $keyword . '%');
                })->get();
            }


            $response['user_list'] = $accountSearchResult;
            $response['status'] = 'success';
            
        } catch (Exception $e) {
            $response['status'] = 'error';
        }
        return response()->json($response);
    }


    /**
     * アカウント申込者をアカウント登録する
     */
    public function registryAccountRequest(Request $request)
    {
        try {

            // ログイン状態（セッションが有効の場合）
            $users = $request->session()->get('users');
            if ($users == null) {
                Log::debug('sesstion timeout.');
                $response['message'] = 'セッションがタイムアウトしました。';
                throw new Exception();
            }

            $id = $request->input('id');

            // アカウント申込を検索する
            $accountRequestSearchResult = St_acm::find($id);

            // 既に登録した場合、ソフトデリートされているため抽出されない
            if ($accountRequestSearchResult == null) {
                $response['message'] = 'データが存在しません。';
                return new Exception();
            }

            // 既に登録済みのメールアドレスの場合エラー
            if (St_act::where('mail', $accountRequestSearchResult['mail'])->exists()) {
                $response['message'] = '既に登録されているメールアドレスです。';
                throw new Exception();
            }

            // アカウントを作成する
            $account_row = new St_act;
            $account_row['mail'] = $accountRequestSearchResult['mail'];
            $account_row['password'] = $accountRequestSearchResult['password'];
            $account_row['account_name'] = $accountRequestSearchResult['account_name'];
            $account_row->save();

            // アカウント申込を削除する
            $accountRequestSearchResult->delete();

            // アイコン画像を設定する
            IconUtil::settingIcon($accountRequestSearchResult['cert_id'].'.png', $account_row['id']);

            $response['status'] = 'success';
            
        } catch (Exception $e) {
            $response['status'] = 'error';
        }
        return response()->json($response);
    }

    /**
     * アカウントのロックを解除する
     */
    public function resetAccountLock(Request $request)
    {
        try {

            // ログイン状態（セッションが有効の場合）
            $users = $request->session()->get('users');
            if ($users == null) {
                Log::debug('sesstion timeout.');
                $response['message'] = 'セッションがタイムアウトしました。';
                throw new Exception();
            }

            $id = $request->input('id');

            St_act::where('id', $id)->update(['login_fail_cnt' => 0, 'login_fail_dt' => null, 'login_lock_cnt' => 0, 'login_lock_dt' => null, 'account_lock' => 0]);

            $response['status'] = 'success';
            
        } catch (Exception $e) {
            $response['status'] = 'error';
        }
        return response()->json($response);
    }
}
