<?php

namespace App\Http\Requests;

class UpdateMessageRequest extends CommonMessageRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'title' => 'required|max:100',
            'message' => 'required'
        ]);
    }
}
