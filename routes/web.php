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
use App\Http\Controllers\ZKDeviceController;
use App\Livewire\Home\Calendar;
use App\Livewire\Base\BankIndex;
use App\Livewire\Base\BusIndex;
use App\Livewire\Base\DocManagerIndex;
use App\Livewire\Base\ImportData;
use App\Livewire\Base\InsuranceOfficeIndex;
use App\Livewire\Base\LocationIndex;
use App\Livewire\Benefits\ConfigurationIndex;
use App\Livewire\Benefits\EmployeeConfiguration;
use App\Livewire\Benefits\PackageIndex;
use App\Livewire\Benefits\VacationPackageIndex;
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
use App\Livewire\Employee\RequestHrLetter;
use App\Livewire\Employee\HrLetterRequests;
use App\Livewire\Employee\OvertimeRequests;
use App\Livewire\Employee\EmployeeOvertimeRequests;
use App\Livewire\Recruitment\ApplicantsCreateMin;
use App\Livewire\Settings\AppLogIndex;


Route::group(['middleware' => ['auth', 'type:admin|hr']], function () {
    Route::get('/', Dashboard::class)->name('home');

    // Payroll & Attendance routes
    Route::get('/attendance/public-holidays', App\Livewire\Attendance\PublicHolidayIndex::class)->name('public-holidays.index');
    Route::get('/payrolls/submit-attendance', App\Livewire\Attendance\AddSheet::class)->name('submit-attendance');
    Route::get('/attendance/bus-arrivals', App\Livewire\Attendance\AddBusArrivals::class)->name('attendance.bus-arrivals');
    Route::get('/attendance/bus-arrivals/records', App\Livewire\Attendance\ShowBusArrivals::class)->name('attendance.bus-arrivals.records');

    Route::get('/attendance/overtime', App\Livewire\Attendance\ShowOvertime::class)->name('overtime.index');
    Route::get('/payrolls', App\Livewire\Payrolls\PayrollIndex::class)->name('payrolls.index');
    Route::get('/payrolls/create', App\Livewire\Payrolls\CreatePayroll::class)->name('payrolls.create');
    Route::get('/payrolls/{id}', App\Livewire\Payrolls\PayrollShow::class)->name('payrolls.show');

    //benefits routes
    Route::get('/benefits/packages', PackageIndex::class)->name('benefits.packages');
    Route::get('/benefits/vacation-packages', VacationPackageIndex::class)->name('benefits.vacation-packages');
    Route::get('/benefits/configurations', ConfigurationIndex::class)->name('benefits.configurations');
    Route::get('/benefits/employee/{employee}', EmployeeConfiguration::class)->name('employee.configuration');
    Route::get('/benefits/bulk-benefits', App\Livewire\Benefits\BulkBenefitsIndex::class)->name('benefits.bulk-benefits');
    Route::get('/benefits/bulk-attendance', App\Livewire\Benefits\BulkAttendanceIndex::class)->name('benefits.bulk-attendance');
    Route::get('/benefits/bulk-vacation', App\Livewire\Benefits\BulkVacationIndex::class)->name('benefits.bulk-vacation');

    Route::get('/hierarchy/tree', [OrganizationController::class, 'index'])->name('hierarchy.tree');
    Route::get('/hierarchy/positions', PositionIndex::class)->name('hierarchy.positions');
    Route::get('/hierarchy/locations', LocationIndex::class)->name('hierarchy.locations');

    Route::get('/recruitment/applicants', ApplicantsIndex::class)->name('recruitment.applicants');
    Route::get('/recruitment/applicants/create', ApplicantsCreate::class)->name('applicants.create');

    Route::get('/recruitment/base-questions', BaseQuestionsIndex::class)->name('recruitment.base-questions');
    Route::get('/recruitment/applicants/success', ApplicantSuccess::class)->name('applicants.success');

    Route::get('/banks', BankIndex::class)->name('banks');
    Route::get('/document-manager', DocManagerIndex::class)->name('document-manager');
    Route::get('/buses', BusIndex::class)->name('buses');
    Route::get('/insurance-offices', InsuranceOfficeIndex::class)->name('insurance-offices');

    // Employee routes
    Route::get('/employees', EmployeeIndex::class)->name('employees');
    Route::get('/employees/create', EmployeeCreate::class)->name('employees.create');
    Route::get('/employees/create/from-applicant/{applicant_id}', EmployeeCreate::class)->name('employees.create.from-applicant');
    Route::get('/employees/import', App\Livewire\Employee\ImportEmployees::class)->name('employees.import');
    Route::get('/employees/dashboard', EmployeeDashboard::class)->name('employees.dashboard');
    Route::get('/employees/reports/missing-documents', MissingDocReport::class)->name('employees.reports.missing-documents');
    Route::get('/employees/{id}', EmployeeShow::class)->name('employees.show');


    Route::get('/settings/users', UsersIndex::class)->name('settings.users');
    Route::get('/settings/areas', AreasIndex::class)->name('settings.areas');
    Route::get('/settings/channels', ChannelIndex::class)->name('settings.channels');

    Route::get('/app-logs', AppLogIndex::class);



    Route::get('employees/requests/hr-letters', HrLetterRequests::class)->name('employees.requests.hr-letters.index');
    Route::get('employees/requests/overtime', OvertimeRequests::class)->name('employees.requests.overtime.index');

    Route::get('/import-data', ImportData::class)->name('import-data');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/recruitment/apply/{vacancyID}/{referralID?}', ApplicantsCreate::class)->name('applicants.guest.create');
    Route::get('/recruitment/apply-min/{vacancyID}/{referralID?}', ApplicantsCreateMin::class)->name('applicants.guest.create-min');
    Route::get('/thank-you', function () {
        return view('thank-you');
    })->name('thank-you');

    Route::get('/iclock/cdata', [ZKDeviceController::class, 'ping']);
    Route::match(['put', 'post'], '/iclock/cdata', [ZKDeviceController::class, 'attendance']);
    Route::get('/iclock/getrequest', [ZKDeviceController::class, 'getRequest']);
    Route::post('/iclock/getrequest', [ZKDeviceController::class, 'getRequest']);
    Route::match(['get', 'post'], '/iclock/devicecmd', [ZKDeviceController::class, 'deviceCmd']);
    
    // Test route to manually force specific time formats (for debugging)
    Route::get('/test-time-format/{serialNumber}/{formatIndex?}', function($serialNumber, $formatIndex = 6) {
        $controller = new \App\Http\Controllers\ZKDeviceController();
        return $controller->forceTimeFormat($serialNumber, $formatIndex);
    });
    
    // Additional ZKTeco endpoints for better compatibility
    Route::match(['get', 'post'], '/iclock/ping', [ZKDeviceController::class, 'ping']);
    Route::match(['get', 'post'], '/iclock', [ZKDeviceController::class, 'ping']);

});


Route::group(['middleware' => ['auth', 'type:employee|hr|admin']], function () {
    Route::get('/attendance', App\Livewire\Attendance\ShowAttendance::class)->name('attendance.index');
    Route::get('/employee/benefits', EmployeeBenefitsView::class)->name('employee.benefits');
    Route::get('/employee/apply-for-vacation', ApplyForVacation::class)->name('employee.apply-for-vacation');
    Route::get('/employee/request-hr-letter', RequestHrLetter::class)->name('employee.request-hr-letter');
    Route::get('/employee/documents', EmployeeDocumentView::class)->name('employee.documents');
    Route::get('/employee/overtime-requests', EmployeeOvertimeRequests::class)->name('employee.overtime-requests');
    Route::get('/attendance/applied-vacation', App\Livewire\Attendance\ShowAppliedVacation::class)->name('applied-vacation.index');
    Route::get('/attendance', App\Livewire\Attendance\ShowAttendance::class)->name('attendance.index');
    Route::get('/recruitment/vacancies/{id}', VacancyShow::class)->name('recruitment.vacancies.show');
    Route::get('/recruitment/vacancies', VacancyIndex::class)->name('recruitment.vacancies');
    Route::get('/recruitment/applicants/{applicant}', ApplicantShow::class)->name('recruitment.applicants.show');
    Route::get('/calendar', Calendar::class)->name('calendar');
    Route::get('/profile', Profile::class);

    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    });
});
