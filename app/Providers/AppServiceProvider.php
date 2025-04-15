<?php

namespace App\Providers;

use App\Models\Base\Area;
use App\Models\Base\City;
use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\OrganizationalChart;
use App\Models\Hierarchy\Position;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Applicants\ApplicantHealth;
use App\Models\Recruitment\Applicants\ApplicantSkill;
use App\Models\Recruitment\Applicants\ApplicationSlot;
use App\Models\Recruitment\Applicants\Education;
use App\Models\Recruitment\Applicants\Experience;
use App\Models\Recruitment\Applicants\Language;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Applicants\ApplicationAnswer;
use App\Models\Recruitment\Applicants\Channel;
use App\Models\Recruitment\Applicants\Education as ApplicantEducation;
use App\Models\Recruitment\Applicants\Experience as ApplicantExperience;
use App\Models\Recruitment\Applicants\Language as ApplicantLanguage;
use App\Models\Recruitment\Applicants\Reference as ApplicantReference;
use App\Models\Recruitment\Applicants\Training as ApplicantTraining;
use App\Models\Recruitment\Interviews\Interview;
use App\Models\Recruitment\Vacancies\BaseQuestion;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Models\Recruitment\Vacancies\VacancyQuestion;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            User::MORPH_NAME => User::class,
            Department::MORPH_NAME => Department::class,
            Position::MORPH_NAME => Position::class,
            Employee::MORPH_NAME => Employee::class,
            Applicant::MORPH_NAME => Applicant::class,
            ApplicantHealth::MORPH_NAME => ApplicantHealth::class,
            ApplicantSkill::MORPH_NAME => ApplicantSkill::class,
            ApplicationSlot::MORPH_NAME => ApplicationSlot::class,
            Education::MORPH_NAME => Education::class,
            Experience::MORPH_NAME => Experience::class,
            Language::MORPH_NAME => Language::class,
            OrganizationalChart::MORPH_NAME => OrganizationalChart::class,
            Application::MORPH_NAME => Application::class,
            ApplicationAnswer::MORPH_NAME => ApplicationAnswer::class,
            ApplicantEducation::MORPH_NAME => ApplicantEducation::class,
            ApplicantExperience::MORPH_NAME => ApplicantExperience::class,
            ApplicantLanguage::MORPH_NAME => ApplicantLanguage::class,
            ApplicantReference::MORPH_NAME => ApplicantReference::class,
            ApplicantTraining::MORPH_NAME => ApplicantTraining::class,
            BaseQuestion::MORPH_NAME => BaseQuestion::class,
            City::MORPH_NAME => City::class,
            Area::MORPH_NAME => Area::class,
            Channel::MORPH_NAME => Channel::class,
            VacancyQuestion::MORPH_NAME => VacancyQuestion::class,
            Vacancy::MORPH_NAME => Vacancy::class,
            Interview::MORPH_NAME => Interview::class,
        ]);
    }
}
