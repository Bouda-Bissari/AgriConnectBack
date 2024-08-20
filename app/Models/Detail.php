<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','email','date','gender','image','bio','company_name','address','domaine'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function service()
{
    return $this->belongsTo(Service::class);
}
}
