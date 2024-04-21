<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\ProductionsManagementController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\UserProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth'], function () {

    Route::redirect('/', '/dashboard');
	Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // Web routes
    Route::get('/production/overview', [ProductionController::class, 'overview'])->name('production.overview');
    Route::get('/production/{production}/analyse', [ProductionController::class, 'productionStatistics']);


    // For handling the form submission
    Route::get('/production/{production}/analyse', [ProductionController::class, 'productionStatistics'])->name('production.analyse');

    Route::get('profile', function () {
		return view('profile');
	})->name('profile');

    //User profile
    Route::get('/user-profile', [UserProfileController::class, 'createProfile']);
    Route::post('/user-profile', [UserProfileController::class, 'storeProfile']);

    Route::get('/logout', [SessionsController::class, 'destroy']);
    Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');

    Route::group(['middleware' => ['auth', 'checkadmin']], function () {
        // Production management
        Route::get('/production-management', [ProductionsManagementController::class, 'index'])->name('production-management.index');

        Route::get('/production-management/{production}/edit', [ProductionsManagementController::class, 'edit']);
        Route::patch('/production-management/{production}', [ProductionsManagementController::class, 'update'])->name('production.update');


        //User management
        Route::get('/user-management', [UserManagementController::class, 'index'])->name('users.index');

        Route::get('/user-management/add', [UserManagementController::class, 'create']);
        Route::post('/user-management', [UserManagementController::class, 'store'])->name('user.store');

        Route::get('/user-management/{user}/edit', [UserManagementController::class, 'edit']);
        Route::patch('/user-management/{user}', [UserManagementController::class, 'update'])->name('user.update');

        Route::get('/user-management/{user}/delete', [UserManagementController::class, 'confirmDelete'])->name('user.confirmDelete');
        Route::delete('/user-management/{user}', [UserManagementController::class, 'destroy'])->name('user.destroy');
    });
});



Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

});
