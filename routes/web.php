<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Home\Dashboard;
use App\Livewire\Organization\PositionIndex;
use App\Livewire\Recruitment\VacancyIndex;
use App\Livewire\Settings\AreasIndex;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\UsersIndex;
use App\Http\Controllers\Hierarchy\OrganizationController;
use App\Livewire\Recruitment\ChannelIndex;

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', Dashboard::class);


    Route::get('/hierarchy/tree', [OrganizationController::class, 'index']);
    Route::get('/hierarchy/positions', PositionIndex::class);

    Route::get('/recruitment/vacancies', VacancyIndex::class);
    Route::get('/settings/users', UsersIndex::class);
    Route::get('/settings/areas', AreasIndex::class);
    Route::get('/settings/channels', ChannelIndex::class);
    Route::get('/profile', Profile::class);

});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', Login::class)->name('login');
});
