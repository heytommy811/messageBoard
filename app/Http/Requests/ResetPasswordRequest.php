<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'cert_id' => 'required|exists:St_apr,cert_id',
            'password' => 'required',
        ];
    }

    
    public function messages()
    {
        return [
            'cert_id.required' => '不正な入力値が指定されました。',
            'cert_id.exists' => '不正な入力値が指定されました。',
        ];
    }
}
