<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return false;
        $allowed_user = [1];
        if(in_array($_SESSION['rfq_access'], $allowed_user)){
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
        if($this->no_quote === 'true' || $this->no_quote_this_time === 'true'){
            return [
                'supplier_name'      => 'required',
                'request_item_id'    => 'required',
            ];
        }
        else{
            return [
                'attachment'         => '',
                'currency'           => 'required',
                'date_served'        => '',
                'lead_time'          => '',
                'moq'                => '',
                'price'              => 'required',
                'rate'               => '',
                'quotation_validity' => '',
                'remarks'            => '',
                'request_item_id'    => 'required',
                'supplier_name'      => 'required',
                'terms_of_payment'   => '',
                'warranty'           => '',
            ];
        }
        
       
    }
}
