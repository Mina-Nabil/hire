<?php

namespace App\Models\Base;

use App\Exceptions\AppException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'arabic_name',
    ];


    ///static methods
    public static function createBank($name, $arabic_name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', self::class)) throw new AppException("You are not authorized to create a bank");


        try {
            return self::firstOrCreate(
                ['name' => $name],
                ['arabic_name' => $arabic_name]
            );
        } catch (Exception $e) {
            report($e);
            throw new AppException("Failed to create bank");
        }
    }

    ///edit methods
    public function editBank($name, $arabic_name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) throw new AppException("You are not authorized to edit this bank");

        try {
            $this->update([
                'name' => $name,
                'arabic_name' => $arabic_name,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException("Failed to edit bank");
        }
    }

    ///delete methods
    public function deleteBank()
    {
        $this->delete();
    }
}
