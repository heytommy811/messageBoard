<?php

namespace App\Http\Requests;

class UpdateMemberAuthRequest extends CommonBoardRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'user_id' => 'required|integer',
            'auth_id' => 'required|in:1,2,3,4',
        ]);
    }
}
