<?php

namespace App\Models\Attendance;

use App\Exceptions\AppException;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PublicHoliday extends Model
{
    protected $fillable = [
        'name',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public $timestamps = false;

    ///static functions 
    //create public holiday
    public static function createPublicHoliday(string $name, Carbon $date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('create', PublicHoliday::class)) {
            throw new AppException('You dont have permission to create public holiday');
        }
        try {
            return self::create([
                'name' => $name,
                'date' => $date,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Error creating public holiday');
        }
    }

    ///edit public holiday
    public function editPublicHoliday(string $name, Carbon $date)
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            throw new AppException('You dont have permission to edit public holiday');
        }
        try {
            $this->update([
                'name' => $name,
                'date' => $date,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Error editing public holiday');
        }
    }

    ///delete public holiday
    public function deletePublicHoliday()
    {
        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('delete', $this)) {
            throw new AppException('You dont have permission to delete public holiday');
        }
        try {
            $this->delete();
        } catch (Exception $e) {
            report($e);
            throw new AppException('Error deleting public holiday');
        }
    }
}
