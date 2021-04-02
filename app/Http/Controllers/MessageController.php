<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\models\St_dgb;
use App\models\St_dgn;
use App\models\St_dgm;
use App\models\St_cmt;
use App\models\St_stk;

use App\modules\common\BoardUtils;
use App\modules\common\Constants;

use Exception;
use App\exceptions\MessageException;

class MessageController extends Controller
{
    /**
     * 伝言IDで指定した伝言の詳細を表示します
     */
    public function index(Request $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        $user_id = $user[Constants::KEY_ID];

        $dgn_id = $request->input('dgn_id');

        // 伝言を検索する
        $message = St_dgn::getMessageDetail($dgn_id, $user_id );
        $dgb_id = $message['dgb_id'];
        $dgn_id = $message['dgn_id'];

        // 伝言板メンバーにユーザーが存在するかチェックする
        St_dgm::validateBoardMember($dgb_id, $user_id);
        
        // 伝言板作成ユーザーの情報を取得する
        try {
            $message_create_user = St_dgm::getMember($dgb_id, $message['create_user_id']);
        } catch (MessageException $e) {
            throw new MessageException('伝言作成ユーザーが存在しません。');
        }
        
        // 対象の伝言のお知らせは削除する
        St_stk::where('dgn_id', $dgn_id)->where('user_id', $user_id)->delete();

        return view('page/message', [
            'message' => $message,    // メッセージの内容
            'canEdit' => $message['create_user_id'] == $user_id,   // 伝言投稿ユーザ自身の場合は編集可能
            'comment_list' => St_cmt::getComments($dgb_id, $dgn_id, $user_id), // コメントの一覧を取得
            // 'dgb_id' => $dgb_id,
            // 'dgb_title' => St_dgb::getBoardTitle($dgb_id),  // 伝言板のタイトル（TODO:画面に出力）
            'create_user_info' => BoardUtils::getMemberInfo($message_create_user)   // 伝言の作成ユーザ情報
        ]);
    }

    /**
     * 伝言登録処理
     */
    public function store(Request $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        $user_id = $user[Constants::KEY_ID];

        // 伝言板メンバーにユーザーが存在するかチェックする
        $dgb_id = $request->input('dgb_id');
        St_dgm::validateBoardMember($dgb_id, $user_id);

        try {

            DB::beginTransaction();

            // 伝言を登録する
            St_dgn::addMessage($request, $user_id);

            // 伝言板を更新する
            St_dgb::updateBoard($dgb_id);

            DB::commit();

            return response()->json([ 'status' => 'success']);

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * 伝言削除処理
     */
    public function destroy(Request $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        St_dgn::deleteMessage($request->input('dgn_id'), $user[Constants::KEY_ID]);
        return response()->json(['status' => 'success']);
    }

    /**
     * 伝言更新処理
     */
    public function update(Request $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        St_dgn::updateMessage($request, $user[Constants::KEY_ID]);
        return $this->index($request);
    }
}
