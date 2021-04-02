<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\models\St_dgb;
use App\modules\common\BoardUtils;
use App\modules\common\Constants;

use Exception;

class SearchController extends Controller
{
    /**
     * 伝言板検索処理
     */
    public function selectBoard(Request $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        return response()->json([
            'board_list' => St_dgb::SelectBoard($request->input('keyword'), $user[Constants::KEY_ID]),
            'status' => 'success'
        ]);
    }
}
