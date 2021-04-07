<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\CommonMessageRequest;

use App\models\St_iin;
use App\modules\common\Constants;

class LikeController extends Controller
{
    /**
     * いいねを登録する
     */
    public function store(CommonMessageRequest $request)
    {
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        $dgn_id = $request->input('dgn_id');
        $user_id = $user[Constants::KEY_ID];

        $like = true;
        if (St_iin::existsLike($dgn_id, $user_id)) {
            // いいね済みの場合は削除する
            $like = false;
            St_iin::deleteLike($dgn_id, $user_id);
        } else {
            // いいねを登録する
            St_iin::addLike($dgn_id, $user_id);
        }

        return response()->json([
            'status' => 'success',
            'liked' => $like,
            'count_like' => St_iin::getLikeCount($dgn_id)
        ]);
    }
}
