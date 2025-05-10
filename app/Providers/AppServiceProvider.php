<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\Base\Area;
use App\Models\Base\City;
use App\Models\Base\InsuranceOffice;
use App\Models\Benefits\Configurations\BaseBenefit;
use App\Models\Benefits\Configurations\BenefitConfiguration;
use App\Models\Benefits\Configurations\BenefitPackage;
use App\Models\Benefits\Configurations\PackageDetail;
use App\Models\Benefits\Configurations\WorkingDay;
use App\Models\Benefits\Extras\Loan;
use App\Models\Benefits\Extras\Purchase;
use App\Models\Benefits\Payrolls\AppliedVacation;
use App\Models\Benefits\Payrolls\BenefitPayment;
use App\Models\Benefits\Payrolls\ExtraPayment;
use App\Models\Benefits\Payrolls\Payroll;
use App\Models\Benefits\Payrolls\PayrollEmployee;
use App\Models\Benefits\Vacations\GainedVacation;
use App\Models\Benefits\Vacations\VacationBenefit;
use App\Models\Benefits\Vacations\VacationDay;
use App\Models\Benefits\Vacations\VacationDetail;
use App\Models\Benefits\Vacations\VacationPayment;
use App\Models\Hierarchy\Department;
use App\Models\Hierarchy\OrganizationalChart;
use App\Models\Hierarchy\Position;
use App\Models\Personel\Docs\BankAccount;
use App\Models\Personel\Docs\DriverLicense;
use App\Models\Personel\Docs\EmployeeContract;
use App\Models\Personel\Docs\EmployeeS1Doc;
use App\Models\Personel\Docs\EmployeeS2Doc;
use App\Models\Personel\Docs\EmployeeS6Doc;
use App\Models\Personel\Docs\HrLetter;
use App\Models\Personel\Docs\IDCard;
use App\Models\Personel\Docs\MedicalRecord;
use App\Models\Personel\Docs\PoliceRecord;
use App\Models\Personel\Docs\PracticeCard;
use App\Models\Personel\Docs\SkillsQualification;
use App\Models\Personel\Docs\SyndicateCard;
use App\Models\Personel\Employee;
use App\Models\Personel\EmployeeInfo;
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
use App\Models\Recruitment\JobOffers\JobOffer;
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
            JobOffer::MORPH_NAME => JobOffer::class,
            EmployeeInfo::MORPH_NAME => EmployeeInfo::class,
            EmployeeContract::MORPH_NAME => EmployeeContract::class,
            BankAccount::MORPH_NAME => BankAccount::class,
            SyndicateCard::MORPH_NAME => SyndicateCard::class,
            PracticeCard::MORPH_NAME => PracticeCard::class,
            EmployeeS1Doc::MORPH_NAME => EmployeeS1Doc::class,
            EmployeeS2Doc::MORPH_NAME => EmployeeS2Doc::class,
            IDCard::MORPH_NAME => IDCard::class,
            DriverLicense::MORPH_NAME => DriverLicense::class,
            MedicalRecord::MORPH_NAME => MedicalRecord::class,
            EmployeeS6Doc::MORPH_NAME => EmployeeS6Doc::class,
            HrLetter::MORPH_NAME => HrLetter::class,
            PoliceRecord::MORPH_NAME => PoliceRecord::class,
            SkillsQualification::MORPH_NAME => SkillsQualification::class,
            InsuranceOffice::MORPH_NAME => InsuranceOffice::class,
            BenefitPackage::MORPH_NAME => BenefitPackage::class,
            BaseBenefit::MORPH_NAME => BaseBenefit::class,
            BenefitConfiguration::MORPH_NAME => BenefitConfiguration::class,
            PackageDetail::MORPH_NAME => PackageDetail::class,
            WorkingDay::MORPH_NAME => WorkingDay::class,
            Loan::MORPH_NAME => Loan::class,
            Purchase::MORPH_NAME => Purchase::class,
            AppliedVacation::MORPH_NAME => AppliedVacation::class,
            VacationBenefit::MORPH_NAME => VacationBenefit::class,
            Payroll::MORPH_NAME => Payroll::class,
            PayrollEmployee::MORPH_NAME => PayrollEmployee::class,
            VacationDay::MORPH_NAME => VacationDay::class,
            VacationDetail::MORPH_NAME => VacationDetail::class,
            VacationPayment::MORPH_NAME => VacationPayment::class,
            GainedVacation::MORPH_NAME => GainedVacation::class,
            PackageDetail::MORPH_NAME => PackageDetail::class,
            BenefitPayment::MORPH_NAME => BenefitPayment::class,
            ExtraPayment::MORPH_NAME => ExtraPayment::class,
        ]);
    }
}
