<?php

namespace App\Models;

use App\Models\QuotationRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestItem extends Model
{
    use HasFactory;

    protected $table = "request_items";
    protected $connection = "mysql";

    public function item_quotation_details(){
        return $this->hasMany(RequestItemQuotation::class,  'request_item_id', 'id');
    }

    public function request_details(){
        return $this->hasOne(QuotationRequest::class, 'id', 'fk_quotation_requests_id');
    }
}
