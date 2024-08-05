<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'first_name', 'last_name','email','age','gender','avatar_url','bio','company_name','address','domaine'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
