<?php

namespace App\Models;

use App\Models\RapidxUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAccess extends Model
{
    use HasFactory;
    protected $connection = "mysql";
    protected $table = "user_accesses";

    public function rapidx_details(){
        return $this->hasOne(RapidxUser::class, 'id', 'rapidx_id');
    }
}
