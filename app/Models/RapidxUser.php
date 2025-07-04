<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapidxUser extends Model
{
    use HasFactory;

    protected $table = "users";
    protected $connection = "mysql_rapidx";
}
