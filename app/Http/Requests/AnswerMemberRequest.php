<?php

namespace App\Http\Requests;

class AnswerMemberRequest extends CommonBoardRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'join_request_id' => 'required|integer',
            'accept' => 'required|integer|in:1,2'
        ]);
    }
}
