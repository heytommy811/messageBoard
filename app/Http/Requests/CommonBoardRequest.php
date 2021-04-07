<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommonBoardRequest extends FormRequest
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
            'dgb_id' => 'required|integer'
        ];
    }

    protected function validationData()
    {
        $validationDataArray = $this->request->all();
        if (!array_key_exists('dgb_id', $validationDataArray)) {
            // NOTE:URLパラメータの場合はrequestにない為、追加必要
            $validationDataArray['dgb_id'] = $this->dgb_id;
        }
        return $validationDataArray;
    }
}
