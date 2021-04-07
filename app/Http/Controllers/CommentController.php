<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\DeleteCommentRequest;

use App\models\St_dgn;
use App\models\St_cmt;

use App\modules\common\Constants;

use Exception;

class CommentController extends Controller
{
    /**
     * 非同期でコメントを投稿し、最新のコメント一覧を取得する
     */
    public function store(CreateCommentRequest $request)
    {

        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        $user_id = $user[Constants::KEY_ID];
        $dgn_id = $request->input('dgn_id');

        // 伝言を検索する
        $message = St_dgn::getMessage($dgn_id);
        $dgb_id = $message['dgb_id'];

        // コメントを登録する
        St_cmt::addComment($dgn_id, $request->input('comment'), $user_id, $message['create_user_id']);

        return response()->json([
            'status' => 'success',
            'comment_list' => St_cmt::getComments($dgb_id, $dgn_id, $user_id)
        ]);
    }

    /**
     * コメントを削除します
     */
    public function destory(DeleteCommentRequest $request) {
        
        // ユーザーのセッション情報を取得
        $user = $this->getUserSession($request);
        $user_id = $user[Constants::KEY_ID];
        
        $dgn_id = $request->input('dgn_id');
        $cmt_id = $request->input('cmt_id');
        
        // 伝言を検索する
        $message = St_dgn::getMessage($dgn_id);
        $dgb_id = $message['dgb_id'];
        
        // コメントを削除
        St_cmt::deleteComment($cmt_id);

        return response()->json([
            'status' => 'success',
            'comment_list' => St_cmt::getComments($dgb_id, $dgn_id, $user_id)
        ]);
    }
}
