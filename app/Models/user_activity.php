<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_activity extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','activity_type', 'activity_time'];
}
