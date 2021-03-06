<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Maklad\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    protected $connection = 'mongodb';
    protected $collection = 'users';

    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'bio',
        'profileImage'
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

    public function products()
    {
        return $this->hasMany(Product::class)->orderByDesc('created_at');
    }

    public function followers()
    {
        return $this->hasOne(Follower::class);
    }

    public function followings()
    {
        return $this->hasOne(Following::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function activeProducts($user){
        $activeProducts = Product::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('is_verified', true)
            ->get();
        //attach user name to products
        return $activeProducts->map(function($product) use ($user){
            $product['name'] = $user->name;
            return $product;
        });
    }

    public function sendPasswordResetNotification($token)
    {
        ResetPasswordNotification::createUrlUsing(function ($notifiable, string $token) {
            return 'http://localhost:3000/reset-password/' . $token . '/' . $notifiable->getEmailForPasswordReset();
        });
        $this->notify(new ResetPasswordNotification($token));
    }
}
