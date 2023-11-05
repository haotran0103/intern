<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class post_history extends Model
{
    use HasFactory;
    protected $fillable = ['post_id', 'user_id ','previous_data', 'updated_data', 'action', 'action_time'];
}
