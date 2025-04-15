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
use App\Livewire\Recruitment\ApplicantShow;
use App\Livewire\Recruitment\ChannelIndex;
use App\Livewire\Recruitment\ApplicantsIndex;
use App\Livewire\Recruitment\VacancyShow;
use Illuminate\Support\Facades\Auth;

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', Dashboard::class)->name('home');


    Route::get('/hierarchy/tree', [OrganizationController::class, 'index']);
    Route::get('/hierarchy/positions', PositionIndex::class);

    Route::get('/recruitment/vacancies/{id}', VacancyShow::class)->name('recruitment.vacancies.show');
    Route::get('/recruitment/vacancies', VacancyIndex::class)->name('recruitment.vacancies');
    Route::get('/recruitment/applicants', ApplicantsIndex::class)->name('recruitment.applicants');
    Route::get('/recruitment/applicants/create', ApplicantsCreate::class)->name('applicants.create');
    Route::get('/recruitment/applicants/{applicant}', ApplicantShow::class)->name('recruitment.applicants.show');
    Route::get('/recruitment/base-questions', BaseQuestionsIndex::class);
    Route::get('/recruitment/applicants/success', ApplicantSuccess::class)->name('applicants.success');
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
