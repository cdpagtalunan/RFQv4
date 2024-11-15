<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;

    protected $table = "request_items";
    protected $connection = "mysql";

    public function item_quotation_details(){
        return $this->hasMany(RequestItemQuotation::class,  'request_item_id', 'id');
    }
}
