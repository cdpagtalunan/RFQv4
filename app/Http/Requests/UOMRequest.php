<?php

namespace App\Http\Requests;

use App\Models\Uom;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class UOMRequest extends FormRequest
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
            'uom_abbrv' => ['required',
                Rule::unique(Uom::class,'uom_abbrv')->ignore($this->id)],
            'uom_desc' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'uom_abbrv.unique' => 'The uom abbrv has already been taken.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Example dynamic message logic
        $message = $this->generateDynamicMessage($validator);

        throw new ValidationException($validator, response()->json([
            'message' => $message,
            'errors' => $validator->errors(),
        ], 422));
    }

    /**
     * For Dynamic message based on uom_abbrv if unique or fields is null
     */
    protected function generateDynamicMessage(Validator $validator)
    {
        // Example: Check if the 'uom_abbrv' field is the only one failing
        if ($validator->errors()->has('uom_abbrv')) {
            $fieldErrors = $validator->errors()->get('uom_abbrv');
            if (in_array('The uom abbrv has already been taken.', $fieldErrors)) {
                return 'The UOM is already taken.';
            }
        }
        return 'Please fill up required fields';
    }
}
