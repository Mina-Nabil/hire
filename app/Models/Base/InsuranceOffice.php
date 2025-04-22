<?php

namespace App\Models\Base;

use App\Exceptions\AppException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InsuranceOffice extends Model
{
    protected $fillable = [
        'name',
        'arabic_name',
    ];


    ///static methods
    public static function createInsuranceOffice($name, $arabic_name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', self::class)) throw new AppException("You are not authorized to create an insurance office");

        try {
            return self::create([
                'name' => $name,
                'arabic_name' => $arabic_name,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException("Failed to create insurance office");
        }
    }

    ///edit methods
    public function editInsuranceOffice($name, $arabic_name)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) throw new AppException("You are not authorized to edit this insurance office");

        try {
            $this->update([
                'name' => $name,
                'arabic_name' => $arabic_name,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException("Failed to edit insurance office");
        }
    }

    ///delete methods
    public function deleteInsuranceOffice()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) throw new AppException("You are not authorized to delete this insurance office");

        try {
            $this->delete();
        } catch (Exception $e) {
            report($e);
            throw new AppException("Failed to delete insurance office");
        }
    }

}
