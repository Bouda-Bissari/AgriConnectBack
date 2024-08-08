<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Service extends Model
{
    use HasApiTokens,HasFactory;


    protected $fillable = [
        'title',
        'description',
        'service_type',
        'deadline',
        'location',
        'price',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function details()
{
    return $this->hasOne(Detail::class);
}
}
