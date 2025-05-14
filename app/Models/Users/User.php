<?php

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Exceptions\AppException;
use App\Models\HrLocationAssignment;
use App\Models\Hierarchy\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{

    public const MORPH_NAME = 'user';
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const TYPE_ADMIN = 'admin';
    const TYPE_HR = 'hr';
    const TYPE_EMPLOYEE = 'employee';

    const TYPES = [
        self::TYPE_ADMIN,
        self::TYPE_HR,
        self::TYPE_EMPLOYEE
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'type',
        'is_active',
        'default_language',
        'image_url'
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
    public function getFullImageUrlAttribute()
    {
        return $this->image_url ? Storage::disk('s3')->url($this->image_url) : null;
    }


    public function getLanguageAttribute()
    {
        return $this->default_language;
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    public function getIsHrAttribute(): bool
    {
        return $this->type === self::TYPE_HR;
    }

    public function getIsEmployeeAttribute(): bool
    {
        return $this->type === self::TYPE_EMPLOYEE;
    }



    ///////static functions
    public static function login($username, $password)
    {
        $user = User::where('username', $username)->where('is_active', true)->first();
        if (!$user) {
            throw new AppException('User not found');
        }
        if (!Hash::check($password, $user->password)) {
            throw new AppException('Invalid password');
        }
        Auth::login($user);
        return $user;
    }

    public static function createUser(string $name, string $username, string $password, string $type, ?string $imageUrl = null)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', User::class)) {
            throw new AppException('You are not authorized to create a user');
        }


        $user = User::create([
            'name' => $name,
            'username' => $username,
            'password' => Hash::make($password),
            'type' => $type,
            'image_url' => $imageUrl
        ]);
        return $user;
    }

    ////model functions
    public function editInfo($name, $username, $type, ?string $imageUrl = null)
    {

        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You are not authorized to update this user');
        }

        if (!in_array($type, self::TYPES)) {
            throw new AppException('Invalid user type');
        }

        $this->name = $name;
        $this->username = $username;
        $this->type = $type;
        if ($this->image_url && $imageUrl != $this->image_url) {
            Storage::disk('s3')->delete($this->image_url);
        }
        $this->image_url = $imageUrl;
        $this->save();
    }

    public function changePassword($password)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You are not authorized to update this user');
        }

        $this->password = Hash::make($password);
        $this->save();
    }


    public function toggleStatus()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You are not authorized to update this user');
        }

        $this->is_active = !$this->is_active;
        $this->save();
    }

    public function setDefaultLanguage(string $language)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You are not authorized to update this user');
        }

        if (!in_array($language, ['en', 'ar'])) {
            throw new AppException('Invalid language');
        }
        $this->default_language = $language;
        $this->save();
    }

    /**
     * Assign locations to an HR user.
     * 
     * @param array $locationIds Array of location IDs to assign to the user
     * @return void
     * @throws AppException If the user is not authorized to update assignments
     */
    public function setAssignedLocations(array $locationIds): void
    {
    
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this) || !$this->is_hr) {
            throw new AppException('You are not authorized to update location assignments or this user is not an HR');
        }

        // Use sync to efficiently manage the pivot relationship
        // This will add new assignments, keep existing ones, and remove those not in the array
        try {
            $this->assignedLocations()->sync($locationIds);
        } catch (\Exception $e) {
            throw new AppException('Error assigning locations: ' . $e->getMessage());
        }
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('username', 'like', '%' . $search . '%');
    }

    public function scopeAdmin($query)
    {
        return $query->where('type', self::TYPE_ADMIN);
    }

    public function scopeHr($query)
    {
        return $query->where('type', self::TYPE_HR);
    }

    public function scopeHrOrAdmin($query)
    {
        return $query->where(function ($query) {
            $query->where('type', self::TYPE_HR)->orWhere('type', self::TYPE_ADMIN);
        });
    }

    public function scopeEmployee($query)
    {
        return $query->where('type', self::TYPE_EMPLOYEE);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeNotEmployee($query)
    {
        return $query->where('type', '!=', self::TYPE_EMPLOYEE);
    }

    /**
     * Get the location assignments for the HR user.
     */
    public function locationAssignments(): HasMany
    {
        return $this->hasMany(HrLocationAssignment::class);
    }
    
    /**
     * Get the locations assigned to the HR user.
     */
    public function assignedLocations()
    {
        return $this->belongsToMany(Location::class, 'hr_location_assignments');
    }

    
}
