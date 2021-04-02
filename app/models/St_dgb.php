<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\models\St_dgn;
use App\models\St_dgm;
use App\models\St_sks;

use App\modules\common\ResponseUtils;

use App\exceptions\MessageException;

class St_dgb extends Model
{
    use SoftDeletes;
    // テーブル名
    protected $table = 'st_dgb';
    // 主キー
    protected $primaryKey = 'dgb_id';
    // ソフトデリートカラム
    protected $dates = ['deleted_at'];
    // 更新可能カラム（ホワイトリスト）
    protected $fillable = ['title', 'create_user_id', 'last_update_date'];

    /**
     * ユーザーの伝言板一覧を取得する
     */
    public function scopeGetBoards($query, $user_id) {
         $boards = $query->select(['st_dgb.dgb_id', 'st_dgb.title'])->join('st_dgm', function ($join) use (&$user_id) {
            $join->on('st_dgb.dgb_id', '=', 'st_dgm.dgb_id')
            ->where('st_dgm.user_id', $user_id);
        })->latest('st_dgb.last_update_date')->get();

        foreach ($boards as $board) {
            $dgb_id = $board['dgb_id'];
            // 最新伝言を取得する
            $last_message = St_dgn::where('dgb_id', $dgb_id)->latest('dgn_id')->first();
            if ($last_message != null) {
                $board['dgn_id'] = $last_message['dgn_id'];
                $board['message_title'] = $last_message['title'];
                $board['last_update_dt'] = ResponseUtils::getUpdateTimeLabel($last_message['updated_at']);
            }
        }
        return $boards;
    }

    /**
     * ユーザーが作成した伝言板の参加申請の一覧を取得する
     */
    public function scopeGetJoinRequestInfomations($query, $user_id) {
        $requests =  $query->join('st_sks', function ($join) {
            // 伝言板申請を結合
            $join->on('st_dgb.dgb_id', '=', 'st_sks.dgb_id')
            ->where('st_sks.request_result', 0);   // 申請結果が1：許可または2：拒否の場合は抽出しない
        })->join('st_act', function ($join) {
            // アカウントを結合
            $join->on('st_sks.user_id', '=', 'st_act.id');
        })->where('st_dgb.create_user_id', $user_id)
        ->select('st_dgb.dgb_id', 'st_dgb.title', 'st_sks.join_request_id', 'st_act.account_name', 'st_sks.created_at')
        ->orderBy('st_sks.join_request_id', 'desc')
        ->get();

        $infomations = [];
        foreach ($requests as $request) {
            $infomations[] =[
                'date' => $request['created_at']->format('Y-m-d H:i'),
                'url' => 'member/' . $request['dgb_id'],
                'dgb_id' => $request['dgb_id'],
                'message' => sprintf('%sさんから%sへの参加申請が来ています。', $request['account_name'], $request['title']),
            ];
        }
        return $infomations;
    }

    /**
     * キーワードで伝言板を検索する
     */
    public function scopeSelectBoard($query, $keyword, $user_id) {
        $boardSearchResults = $query->join('st_dgk', function ($join) {
            $join->on('st_dgk.dgb_id', '=', 'st_dgb.dgb_id')
            ->where('search_type', 1);  // 検索可能な伝言板のみが対象
        })->join('st_dgm', function ($join) {
            $join->on('st_dgm.dgb_id', '=', 'st_dgb.dgb_id')
            ->on('st_dgm.user_id', '=', 'st_dgb.create_user_id');
        })->where(function($query) use ($keyword) {
            $query->orWhere('st_dgb.title', 'LIKE', '%' . $keyword . '%')
            ->orWhere('st_dgb.dgb_id', $keyword); // 伝言板IDの完全一致でも検索可能
        })->get();

        foreach($boardSearchResults as $boardSearchResult) {
            $dgb_id = $boardSearchResult['dgb_id'];
            // 伝言板メンバーか検索する
            if (St_dgm::isBoardMember($dgb_id, $user_id)) {
                $button_type = '3';
            } else if (St_sks::hasRequest($dgb_id, $user_id)) {
                // 申請中か検索する
                $button_type = '2';
            } else {
                // 参加可能
                $button_type = '1';
            }
            $boardSearchResult['button_type'] = $button_type;
        }

        return $boardSearchResults;
    }

    /**
     * 伝言板と伝言板管理を取得する
     */
    public function scopeGetBoardWithManage($query, $dgb_id) {
        $board = $query->join('st_dgk', function ($join) {
            $join->on('st_dgk.dgb_id', '=', 'st_dgb.dgb_id');
        })->where('st_dgb.dgb_id', $dgb_id)->first();
        if ($board == null) {
            Log::debug(sprintf('board does not exists.[dgbid:%s]', $dgb_id));
            throw new MessageException('伝言板が存在しません。');
        }
        return $board;
    }

    /**
     * 伝言板を登録する
     */
    public function scopeAddBoard($query, $title, $user_id) {

        $dgb = $query->create([
            'title' => $title,
            'create_user_id' => $user_id,
            'last_update_date' => Carbon::now()
        ]);
        return $dgb['dgb_id'];
    }

    /**
     * 伝言版を検索する
     * 存在しない場合はエラーメッセージを表示する
     */
    public function scopeGetBoard($query, $dgb_id) {
        $boardSearchResult = $query->where('dgb_id', $dgb_id)->first();
        if ($boardSearchResult == null) {
            Log::debug(sprintf('board does not exists.[dgb_id:%s]', $dgb_id));
            throw new MessageException('伝言板が存在しません。');
        }

        return $boardSearchResult;
    }

    /**
     * 伝言板が存在するかチェックします
     */
    public function scopeValidateBoard($query, $dgb_id) {
        if (!$query->where('dgb_id', $dgb_id)->exists()) {
            Log::debug(sprintf('board does not exists.[dgb_id:%s]', $dgb_id));
            throw new MessageException('伝言板が存在しません。');
        }
    }

    /**
     * 伝言板の更新日時を更新する
     */
    public function scopeUpdateBoard($query, $dgb_id) {
        $reuslt = $query->where('dgb_id', $dgb_id)->first()->update([
            'last_update_date' => Carbon::now()
        ]);
        if ($reuslt === false) {
            Log::error(sprintf('board does not exists.[dgb_id:%s]', $dgb_id));
            throw new MessageException('伝言板が存在しません。');
        }
    }

    /**
     * 伝言板のタイトルを取得する
     */
    public function scopeGetBoardTitle($query, $dgb_id) {
        $board = St_dgb::where('dgb_id', $dgb_id)->first();
        if ($board == null) {
            Log::error(sprintf('board does not exists.[dgb_id:%s]', $dgb_id));
            throw new MessageException('伝言板が存在しません。');
        }
        return $board['title'];
    }
}
