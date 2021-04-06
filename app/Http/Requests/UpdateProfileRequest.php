<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'account_name' => 'required|string|max:10',
            'file' => 'image|mimes:png,jpg,jpeg',
            'width' => 'required_with:file|numeric',
            'height' => 'required_with:file|numeric',
            'x' => 'required_with:file|numeric',
            'y' => 'required_with:file|numeric',
        ];
    }

    public function messages()
    {
        return [
            'width.required_with' => '不正な入力値が指定されました。',
            'width.numeric' => '不正な入力値が指定されました。',
            'height.required_with' => '不正な入力値が指定されました。',
            'height.numeric' => '不正な入力値が指定されました。',
            'x.required_with' => '不正な入力値が指定されました。',
            'x.numeric' => '不正な入力値が指定されました。',
            'y.required_with' => '不正な入力値が指定されました。',
            'y.numeric' => '不正な入力値が指定されました。',
        ];
    }
}
