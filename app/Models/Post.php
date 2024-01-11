<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'title',
        'content',
        'status',
        'user_id',
        'file',
    ];
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

}
