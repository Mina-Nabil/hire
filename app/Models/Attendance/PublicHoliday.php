<?php

namespace App\Models\Attendance;

use App\Exceptions\AppException;
use App\Models\Users\AppLog;
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
            AppLog::info('Public Holiday Created', "Name: $name created at $date");
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error creating public holiday', $e->getMessage());
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
            AppLog::info('Public Holiday Updated', "Name: $name updated at $date", loggable: $this);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error editing public holiday', $e->getMessage());
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
            AppLog::info('Public Holiday Deleted', "Name: $this->name deleted at $this->date");
        } catch (Exception $e) {
            report($e);
            AppLog::error('Error deleting public holiday', $e->getMessage());
            throw new AppException('Error deleting public holiday');
        }
    }
}
