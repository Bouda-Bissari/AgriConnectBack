<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\DatabaseNotification;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone_number',
        'fullName',
        'password',
        'is_completed',
    ];



    public function routeNotificationForMail()
    {
        // Utilisation de la relation 'details' pour récupérer l'email
        return $this->details->email;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function details()
    {
        return $this->hasOne(Detail::class);
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }
    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }
    public function reports()
{
    return $this->hasMany(Report::class);
}


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phone_number_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

}
