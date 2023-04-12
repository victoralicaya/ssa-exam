<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

// use Illuminate\Contracts\Auth\Authenticatable;

use App\Services\UserService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prefixname',
        'firstname',
        'middlename',
        'lastname',
        'suffixname',
        'username',
        'photo',
        'email',
        'password',
    ];

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(Detail::class);
    }

    public function getAvatarAttribute():? string
    {
        return $this->photo ?? '';
    }

    public function getFullnameAttribute(): string
    {
        return $this->firstname.' '.$this->getMiddleinitialAttribute().' '.$this->lastname;
    }


    public function getMiddleinitialAttribute(): string
    {
        $middlePeriod = !empty($this->middlename) ? '.' : '';
        return substr($this->middlename,0,1).$middlePeriod;
    }

    protected static function boot()
    {
        parent::boot();

        // delete file when user is deleted
        static::deleting(function ($user) {
            Storage::disk('public')->delete('/images/'.basename($user->photo));
        });

        static::creating(function ($user){
            $user->password = (new UserService())->hash($user->password);
        });

        static::updating(function ($user) {
            if ($user->isDirty('password')) {
                $user->password = (new UserService())->hash($user->password);
            }
        });
    }
}
