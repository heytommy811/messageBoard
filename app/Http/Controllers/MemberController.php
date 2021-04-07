<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\CommonBoardRequest;
use App\Http\Requests\UpdateMemberAuthRequest;
use App\Http\Requests\AnswerMemberRequest;
use App\Http\Requests\UpdateMemberNameRequest;

use App\models\St_dgb;
use App\models\St_dgm;
use App\models\St_dgk;
use App\models\St_sks;
use App\modules\common\BoardUtils;
use App\modules\common\Constants;

use App\exceptions\MessageException;

class MemberController extends Controller
{
    /**
     * 伝言IDで指定した伝言のメンバー一覧を表示します
     */
    public function index(CommonBoardRequest $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        $user_id = $user[Constants::KEY_ID];
        $dgb_id = $request->input('dgb_id');
        
        // 伝言板メンバーにユーザーが存在するかチェックする
        if (!St_dgm::isBoardMember($dgb_id, $user_id)) {
            throw new MessageException('閲覧権限がありません。');
        }

        // 伝言板作成者IDを取得する
        $board = St_dgb::getBoard($dgb_id);
        $create_user_id = $board['create_user_id'];

        // 伝言板メンバー一覧を取得する
        $members = St_dgm::getMembers($dgb_id);

        // 作成者のメンバー情報を取得する
        $member_list = [];
        $is_manage_user = $create_user_id == $user_id;
        foreach ($members as $member) {
            $member_list[] = BoardUtils::getMemberInfo($member);
            if ($member['user_id'] == $user_id && $member['auth_id'] == 5) {
                // ログインユーザーの権限が管理者の場合
                $is_manage_user = true;
            }
        }

        return response()->json([
            'member_list' => $member_list,
            'is_manage_user' => $is_manage_user,
            'dgb_title' => $board['title'],
            'join_request_list' => $is_manage_user ? St_sks::getRequestUsers($dgb_id) : [], // 作成者の場合は申請者一覧を取得する
            'status' => 'success'
        ]);
    }

    /**
     * メンバーの権限を更新する非同期処理
     */
    public function update(UpdateMemberAuthRequest $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        St_dgm::updateMemberAuth($request, $user[Constants::KEY_ID]);
        return response()->json(['status' => 'success']);
    }

    /**
     * 参加申請処理
     */
    public function request(CommonBoardRequest $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        $user_id = $user[Constants::KEY_ID];
        $dgb_id = $request->input('dgb_id');

        // 伝言板が存在するか確認する
        $boardSearchResult = St_dgb::getBoardWithManage($dgb_id);

        // 既に伝言板メンバー
        if (!St_dgm::isBoardMember($dgb_id, $user_id)) {
            Log::debug(sprintf('alredy board member.[dgb_id:%s] [user_id:%s]', $dgb_id, $user_id));
            throw new MessageException('既に伝言板に参加しています。');
        }

        if ($boardSearchResult["join_type"] == 3 || $boardSearchResult["join_type"] == 9) {
            // パスワード認証
            $password = $request->input('password');
            if (empty($password)) {
                Log::debug(sprintf('password is empty.[password:%s]', $password));
                throw new MessageException('パスワードを入力してください。');
            }

            if ($boardSearchResult["join_password"] != $password) {
                throw new MessageException('パスワードが不正です。');
            }
        }

        if ($boardSearchResult["join_type"] == 2 || $boardSearchResult["join_type"] == 9) {

            // 申請処理
            // 参加申請に登録する
            St_sks::addRequest($dgb_id, $user_id);
            $result = 'requested';
        } else {

            // 参加処理
            // 伝言板メンバーに登録する
            St_dgm::addMember($dgb_id, $user_id, $boardSearchResult['default_auth_id']);
            $result = 'joined';
        }

        return response()->json([
            'result' => $result,
            'status' => 'success'
        ]);
    }

    /**
     * 参加申請結果を回答する
     */
    public function answer(AnswerMemberRequest $request)
    {

        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        St_sks::updateRequestResult($request);
        return response()->json(['status' => 'success']);
    }

    /**
     * 伝言板参加時にメンバー名を登録します。
     */
    public function store(UpdateMemberNameRequest $request)
    {

        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        $user_id = $user[Constants::KEY_ID];
        $dgb_id = $request->input('dgb_id');

        // 伝言板メンバーにユーザーが存在するかチェックする
        if (!St_dgm::isBoardMember($dgb_id, $user_id)) {
            Log::debug(sprintf('user does not member.[dgb_id:%s] [user_id:%s]', $dgb_id, $user_id));
            throw new MessageException('伝言板に参加していません。');
        }

        St_dgm::updateMemberName($dgb_id, $user_id, $request->input('name'));
       
        return response()->json(['status' => 'success']);
    }
}
