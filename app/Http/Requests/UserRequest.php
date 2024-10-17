<?php

namespace App\Http\Requests;

use App\Models\UserAccess;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // session_start();
        $allowed_user = [1,2];
        if(in_array($_SESSION['rfq_type'], $allowed_user)){
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
        // dd($this->id);
        return [
            'rapidx_id' => ['required',
            Rule::unique(UserAccess::class,'rapidx_id')->ignore($this->id)],
            'user_access' => 'required',
            'user_type' => 'required'
        ];
    }
}
