<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dgb_id' => 'required|integer',
            'title' => 'required|max:100',
            'message' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'title' => '伝言タイトル',
        ];
    }
}
