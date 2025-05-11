<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Home\Dashboard;
use App\Livewire\Organization\PositionIndex;
use App\Livewire\Recruitment\VacancyIndex;
use App\Livewire\Recruitment\BaseQuestionsIndex;
use App\Livewire\Recruitment\ApplicantsCreate;
use App\Livewire\Recruitment\ApplicantSuccess;
use App\Livewire\Settings\AreasIndex;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\UsersIndex;
use App\Http\Controllers\Hierarchy\OrganizationController;
use App\Livewire\Base\BankIndex;
use App\Livewire\Base\InsuranceOfficeIndex;
use App\Livewire\Benefits\ConfigurationIndex;
use App\Livewire\Benefits\EmployeeConfiguration;
use App\Livewire\Benefits\PackageIndex;
use App\Livewire\Employee\EmployeeBenefitsView;
use App\Livewire\Employee\EmployeeDashboard;
use App\Livewire\Employee\EmployeeIndex;
use App\Livewire\Employee\EmployeeShow;
use App\Livewire\Employee\EmployeeCreate;
use App\Livewire\Employee\MissingDocReport;
use App\Livewire\Recruitment\ApplicantShow;
use App\Livewire\Recruitment\ChannelIndex;
use App\Livewire\Recruitment\ApplicantsIndex;
use App\Livewire\Recruitment\VacancyShow;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Employee\ApplyForVacation;
use App\Livewire\Employee\EmployeeDocumentView;

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', Dashboard::class)->name('home');

    // Payroll & Attendance routes
    Route::get('/attendance/public-holidays', App\Livewire\Attendance\PublicHolidayIndex::class)->name('public-holidays.index');
    Route::get('/payrolls/submit-attendance', App\Livewire\Attendance\AddSheet::class)->name('submit-attendance');

    //benefits routes
    Route::get('/benefits/packages', PackageIndex::class)->name('benefits.packages');
    Route::get('/benefits/configurations', ConfigurationIndex::class)->name('benefits.configurations');
    Route::get('/benefits/employee/{employee}', EmployeeConfiguration::class)->name('employee.configuration');

    Route::get('/hierarchy/tree', [OrganizationController::class, 'index']);
    Route::get('/hierarchy/positions', PositionIndex::class);

    Route::get('/recruitment/vacancies/{id}', VacancyShow::class)->name('recruitment.vacancies.show');
    Route::get('/recruitment/vacancies', VacancyIndex::class)->name('recruitment.vacancies');
    Route::get('/recruitment/applicants', ApplicantsIndex::class)->name('recruitment.applicants');
    Route::get('/recruitment/applicants/create', ApplicantsCreate::class)->name('applicants.create');
    Route::get('/recruitment/applicants/{applicant}', ApplicantShow::class)->name('recruitment.applicants.show');
    Route::get('/recruitment/base-questions', BaseQuestionsIndex::class);
    Route::get('/recruitment/applicants/success', ApplicantSuccess::class)->name('applicants.success');
    
    Route::get('/banks', BankIndex::class)->name('banks');
    Route::get('/insurance-offices', InsuranceOfficeIndex::class)->name('insurance-offices');

    // Employee routes
    Route::get('/employees', EmployeeIndex::class)->name('employees');
    Route::get('/employees/create', EmployeeCreate::class)->name('employees.create');
    Route::get('/employees/create/from-applicant/{applicant_id}', EmployeeCreate::class)->name('employees.create.from-applicant');
    Route::get('/employees/dashboard', EmployeeDashboard::class)->name('employees.dashboard');
    Route::get('/employees/reports/missing-documents', MissingDocReport::class)->name('employees.reports.missing-documents');
    Route::get('/employees/{id}', EmployeeShow::class)->name('employees.show');
    Route::get('/employee/benefits', EmployeeBenefitsView::class)->name('employee.benefits');
    Route::get('/employee/apply-for-vacation', ApplyForVacation::class)->name('employee.apply-for-vacation');
    Route::get('/employee/documents', EmployeeDocumentView::class)->name('employee.documents');
    
    Route::get('/settings/users', UsersIndex::class);
    Route::get('/settings/areas', AreasIndex::class);
    Route::get('/settings/channels', ChannelIndex::class);
    Route::get('/profile', Profile::class);

    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    });

});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/recruitment/apply/{vacancyID}/{referralID?}', ApplicantsCreate::class)->name('applicants.guest.create');
});
