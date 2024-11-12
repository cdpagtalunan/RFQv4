<?php

namespace App\Http\Requests;

use App\Models\Currency;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
        return [
            'currency' =>  ['required',
            Rule::unique(Currency::class,'currency')->ignore($this->id)]
        ];
    }
}
