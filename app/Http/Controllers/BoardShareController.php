<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\models\St_dgb;
use App\models\St_dgm;
use App\models\St_dgs;

use App\modules\common\Constants;
use App\modules\common\ResponseUtils;
use App\modules\common\BoardUtils;

use Carbon\Carbon;

use Exception;
use App\exceptions\MessageException;

class BoardShareController extends Controller
{
    /**
     * 伝言板の共有URLを取得する
     */
    public function store(Request $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        $user_id = $user[Constants::KEY_ID];
        $dgb_id = $request->input('dgb_id');

        // 伝言板の存在確認を行う
        St_dgb::validateBoard($dgb_id);

        // 共有権限を検証する
        St_dgm::validateShareAuth($dgb_id, $user_id);

        // 伝言板招待に登録する
        $invite_id = uniqid();
        St_dgs::addInvite($dgb_id, $invite_id, $user_id);

        return response()->json([
            'share_url' => url("/share/{$invite_id}"),
            'status' => 'success'
        ]);
    }

    /**
     * 共有URLを実行した場合
     */
    public function index(Request $request, $invite_id)
    {

        $user = $request->session()->get('users');
        if ($user == null) {
            // 未ログインの場合
            $request->session()->regenerate();
            $request->session()->put('invite_id', $invite_id);
            return redirect("login");
        }

        $request->session()->forget('invite_id');
        // 伝言板招待を検索する
        $invite = St_dgs::getInvite($invite_id);

        $dgb_id = $invite['dgb_id'];
        try {
            $boardSearchResult = St_dgb::getBoardWithManage($dgb_id);
        } catch (Exception $e) {
            // 伝言板が存在しない場合はエラー
            throw new MessageException('該当する伝言板がありません。', 'home');
        }

        // 既にメンバーの場合
        if (St_dgm::isBoardMember($dgb_id, $user[Constants::KEY_ID])) {
            Log::debug(sprintf('already member.[dgb_id:%s] [user_id:%s]', $dgb_id, $user[Constants::KEY_ID]));
            throw new MessageException('既に参加しています。', 'home', ['page' => 'board', 'd' => $dgb_id]);
        }

        // 共有されたため、ユーザーを伝言板に登録する
        St_dgm::addMember($dgb_id, $user[Constants::KEY_ID], $boardSearchResult['default_auth_id']);

        $request->session()->put('dgb_id', $dgb_id);

        // 伝言板のページ用のパラメータを設定することで初期表示時に遷移させる
        return redirect()->route('home', ['page' => 'board', 'd' => $dgb_id]);
    }
}
