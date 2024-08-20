<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // If you have additional fields that are mass assignable
    protected $fillable = [
        'user_id',
        'service_id',
        'description',
    ];

    /**
     * Get the user that owns the report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service that is reported.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
