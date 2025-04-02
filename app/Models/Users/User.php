<?php

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Exceptions\AppException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'default_language'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'password' => 'hashed',
        ];
    }

    ///////attributes
    public function getLanguageAttribute()
    {
        return $this->default_language;
    }

    ///////static functions
    public static function login($username, $password)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            throw new AppException('User not found');
        }
        if (!Hash::check($password, $user->password)) {
            throw new AppException('Invalid password');
        }
        Auth::login($user);
        return $user;
    }


    ///////model functions
    public function setDefaultLanguage(string $language)
    {
        if (!in_array($language, ['en', 'ar'])) {
            throw new AppException('Invalid language');
        }
        $this->default_language = $language;
        $this->save();
    }
    
    
}
