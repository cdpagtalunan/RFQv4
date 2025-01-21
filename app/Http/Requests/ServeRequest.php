<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if($_SESSION['rfq_approver'] == 1){
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'remarks' => 'required'
        ];
    }
}
