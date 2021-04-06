<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\CreateBoardRequest;

use App\models\St_dgb;
use App\models\St_dgn;
use App\models\St_dgk;
use App\models\St_dgm;
use App\models\St_stk;

use App\modules\common\Constants;

use Exception;

class BoardController extends Controller
{
    /**
     * トップ画面で表示する伝言板の一覧を取得する
     */
    public function all(Request $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);

        // お知らせ１：作成した伝言板の参加申請の有無
        $joinRequestInfomations = St_dgb::getJoinRequestInfomations($user[Constants::KEY_ID]);

        // お知らせ２：作成した伝言に対するコメントといいね
        $stkInfomations = St_stk::getReplyInfomations($user[Constants::KEY_ID]);

        return view('page/home', [
            'board_list' => St_dgb::getBoards($user[Constants::KEY_ID]),
            'infomation_list' => array_merge($joinRequestInfomations, $stkInfomations)
        ]);
    }

    /**
     * 伝言板IDで指定した伝言版の伝言一覧を表示する
     */
    public function index(Request $request)
    {
        $request->validate([
            'dgb_id' => 'required|integer'
        ]);

        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);

        $dgb_id = $request->input('dgb_id');

        // 伝言板を検索する
        $boardSearchResult = St_dgb::getBoard($dgb_id);

        // 伝言板メンバーを取得してメンバー名が設定されているか判定する
        $searchMemberResult = St_dgm::getMember($dgb_id, $user[Constants::KEY_ID]);
        if ($searchMemberResult['name'] == null) {
            return redirect('board/join/' . $dgb_id);
        }

        // 伝言リストを取得する
        $messages = St_dgn::getMessages($dgb_id);

        return view('page/board', [
            'title' => $boardSearchResult['title'], // TODO:画面に出力
            'message_list' => $messages,
            'dgb_id' => $dgb_id,
            'auth_id' => $searchMemberResult['auth_id']
        ]);
    }

    /**
     * 伝言板参加画面を表示する
     */
    public function join(Request $request, $dgb_id)
    {
        $request->validate([
            'dgb_id' => 'required|integer'
        ]);
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);

        // 伝言板を検索する
        $boardSearchResult = St_dgb::getBoard($dgb_id);

        // 伝言板メンバーにユーザーが存在するかチェックする
        St_dgm::validateBoardMember($dgb_id, $user[Constants::KEY_ID]);

        return view('page/joinBoard', [
            'title' => $boardSearchResult['title'],
            'name' => $user[Constants::KEY_ACCOUNT_NAME],
            'dgb_id' => $dgb_id
        ]);
    }

    /**
     * 伝言板を新規作成します
     */
    public function store(CreateBoardRequest $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);

        try {

            // トランザクションの開始
            DB::beginTransaction();
            
            // 伝言板テーブルのレコードを登録
            $dgb_id = St_dgb::addBoard($request->input('title'), $user[Constants::KEY_ID]);

            // 伝言板管理のレコードを登録
            St_dgk::addBoardManage($request, $dgb_id);
            
            // 作成者のメンバー登録
            St_dgm::addMember($dgb_id, $user[Constants::KEY_ID], 5, $request->input('manage_user_name'));

            // コミット
            DB::commit();

            return response()->json([
                'status' => 'success'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
