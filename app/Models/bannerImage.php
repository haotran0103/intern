<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bannerImage extends Model
{
    use HasFactory;
    protected $table = 'banner_images'; 
    protected $fillable = [
        'image_url',
        'status',
    ];
}
