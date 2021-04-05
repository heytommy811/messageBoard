<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBoardRequest extends FormRequest
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
            'title' => 'required',
            'manage_user_name' => 'required',
            'join_type' => 'required|in:1,2,3,9',
            'join_password' => 'required_if:join_type,3,9', // 参加種別が3:パスワードのみ、または9:パスワードと作成社による承認の場合は必須
            'search_type' => 'required|boolean',
            'default_auth_id' => 'required|in:1,2,3,4',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'タイトル',
            'manage_user_name' => '作成者名',
            'join_type' => '参加方式',
            'join_password' => 'パスワード',
            'search_type' => '検索可否',
            'default_auth_id' => '参加者の権限',
        ];
    }

    public function messages()
    {
        return [
            'join_password.required_if' => ':otherで「パスワードで認証する」をONにした場合は:attributeを入力してください。',
        ];
    }
}
