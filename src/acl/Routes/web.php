<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" Middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');


Auth::routes();

Route::get('/validate/email/{token}', 'HomeController@validateEmail')->name('validate_email');


/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::get('/profile/index', 'ProfileController@index')->name('profile_index');
Route::get('/profile/{name}/show', 'ProfileController@show')->name('profile_show');
Route::group(['prefix' => 'profile', 'middleware' => ['auth', 'isactive', 'can:edit_profile']], function () {
    Route::get('/edit', 'ProfileController@edit')->name('profile_edit');
    Route::post('/store', 'ProfileController@store')->name('profile_store');
    Route::post('/store/avatar', 'ProfileController@storeAvatar')->name('profile_store_avatar');
    Route::post('/delete/avatar', 'ProfileController@deleteAvatar')->name('profile_delete_avatar');
});


/*
|--------------------------------------------------------------------------
| Permission Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'isactive', 'role:admin']], function () {
    Route::get('/dashboard', 'DashboardController@dashboard')->name('admin_dashboard');
    Route::get('/permission', 'DashboardController@permission')->name('admin_permission');
    Route::get('/account', 'DashboardController@account')->name('admin_account');
    Route::get('/account/{id}/detail', 'DashboardController@accountDetail')->name('admin_account_detail');
    Route::get('/logs', 'DashboardController@logs')->name('admin_logs');
    Route::get('/logs/{name}', 'DashboardController@logDetail')->name('admin_log_detail');

    Route::post('/change/permission', 'PermissionController@changeRolePermission');
    Route::post('/change/role', 'PermissionController@changeUserRole');
    Route::post('/change/profile', 'PermissionController@changeUserProfile');
    Route::post('/change/user-status', 'PermissionController@changeUserStatus');
    Route::post('/store/avatar', 'PermissionController@storeAvatar')->name('admin_store_avatar');
    Route::post('/delete/avatar', 'PermissionController@deleteAvatar')->name('admin_delete_avatar');
});