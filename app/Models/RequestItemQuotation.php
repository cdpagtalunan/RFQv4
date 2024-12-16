<?php

namespace App\Models;

use App\Models\RequestItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestItemQuotation extends Model
{
    use HasFactory;
    protected $table = 'request_item_quotations';
    protected $connection = 'mysql';

    public function request_item_details(){
        return $this->hasOne(RequestItem::class,'id', 'request_item_id');
    }
}
