<?php

namespace App\Models\Recruitment\Applicants;

use App\Exceptions\AppException;
use App\Models\Recruitment\Vacancies\VacancySlot;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationSlot extends Model
{
    const MORPH_NAME = 'application_slot';
    
    protected $table = 'application_slots';
    
    public $timestamps = false;
    
    protected $fillable = [
        'application_id',
        'vacancy_slot_id',
    ];

    /**
     * Get the application this slot belongs to
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the vacancy slot this application slot is for
     */
    public function vacancySlot(): BelongsTo
    {
        return $this->belongsTo(VacancySlot::class);
    }

    /**
     * Book a vacancy slot for an application
     * 
     * @param int $applicationId
     * @param int $vacancySlotId
     * @return ApplicationSlot
     */
    public static function bookSlot(int $applicationId, int $vacancySlotId): ApplicationSlot
    {
        try {
            return self::create([
                'application_id' => $applicationId,
                'vacancy_slot_id' => $vacancySlotId,
            ]);
        } catch (Exception $e) {
            report($e);
            throw new AppException('Failed to book slot: ' . $e->getMessage());
        }
    }
} 