<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\UpdateManageRequest;

use App\models\St_dgb;
use App\models\St_dgm;
use App\models\St_dgk;

use App\modules\common\Constants;

class BoardManageController extends Controller
{
    /**
     * 伝言板管理画面を表示する
     */
    public function index(Request $request)
    {
        $request->validate([
            'dgb_id' => 'required|integer'
        ]);
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        $dgb_id = $request->input('dgb_id');

        // 権限を検証する
        St_dgm::validateManageAuth($dgb_id, $user[Constants::KEY_ID]);

        return response()->json([
            'dgb_id' => $dgb_id,
            'dgb_title' => St_dgb::getBoardTitle($dgb_id),  // 伝言板タイトル
            'manage' => St_dgk::getBoardManage($dgb_id),    // 伝言板管理情報
            'status' => 'success'
        ]);
    }

    /**
     * 伝言板管理を更新する
     */
    public function update(UpdateManageRequest $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);

        // 権限を検証する
        St_dgm::validateManageAuth($request->input('dgb_id'), $user[Constants::KEY_ID]);

        // 伝言板管理を更新する
        St_dgk::updateManage($request);

        return response()->json(['status' => 'success']);
    }
}
