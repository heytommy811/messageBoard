<?php

namespace App\Http\Requests;

class UpdateMemberNameRequest extends CommonBoardRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'required|max:100',
        ]);
    }
}
