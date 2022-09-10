<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'name',
        'username',
        'password',
        'information',
        'role',
        'level'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function getVerifiedAtAttribute($value) {
        if (isset($value))
            return dateConvert($value);
    }

    public function getCreatedAtAttribute($value) {
        if (isset($value))
            return dateConvert($value);
    }

    public function getUpdatedAtAttribute($value) {
        if (isset($value))
            return dateConvert($value);
    }

    public function findForPassport($username) {
        return $this->where('username', $username)->first();
    }

    public function validateForPassportPasswordGrant($password) {
        return Hash::check($password, $this->password);
    }
}