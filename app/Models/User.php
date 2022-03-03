<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Compoships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_id',
        'signature',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->hasOne(\App\Models\Employees\Agents::class, 'email', 'email');
    }

    public function loan_officer()
    {
        return $this->hasOne(\App\Models\Employees\Mortgage::class, 'email', 'email');
    }

    public function credit_cards()
    {
        return $this->hasMany(\App\Models\Billing\CreditCards::class, 'user_id', 'id');
    }
}
