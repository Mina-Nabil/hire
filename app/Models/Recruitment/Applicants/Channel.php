<?php

namespace App\Models\Recruitment\Applicants;

use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public $timestamps = false;

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'channel_id');
    }

    public static function newChannel(string $name): self|false
    {
        try {
            $channel = new self([
                'name' => $name
            ]);

            $channel->save();

            AppLog::info('Applicant channel created successfully');
            return $channel;
        } catch (Exception $e) {
            AppLog::error('Creating applicant channel failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function editInfo(string $name): bool
    {
        try {
            $this->name = $name;

            if ($this->save()) {
                AppLog::info('Applicant channel updated successfully', loggable: $this);
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Updating applicant channel failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function deleteChannel(): bool
    {
        try {
            $this->delete();

            AppLog::info('Applicant channel deleted successfully', loggable: $this);
            return true;
        } catch (Exception $e) {
            AppLog::error('Deleting applicant channel failed', $e->getMessage());
            report($e);
            return false;
        }
    }
} 