<?php

namespace App\Models;

use App\Models\Category;
use App\Models\RequestItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationRequest extends Model
{
    use HasFactory;

    protected $table = 'quotation_requests';
    protected $connection = 'mysql';

    public function category_details(){
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function item_details(){
        return $this->hasMany(RequestItem::class,'fk_quotation_requests_id', 'id');
    }

    public function created_by_details(){
        return $this->hasOne(RapidxUser::class, 'id', 'created_by');
    }

    public function assigned_to_details(){
        return $this->hasOne(RapidxUser::class, 'id', 'assigned_to');
    }
    
    public function assigned_by_details(){
        return $this->hasOne(RapidxUser::class, 'id', 'assigned_by');
    }
}
