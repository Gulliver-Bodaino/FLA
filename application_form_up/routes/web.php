<?php

use Illuminate\Support\Facades\Route;

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

/*
Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
*/

// 表画面
Route::namespace('App\Http\Controllers\Frontend')->group(function () {
//    Route::get('/a', 'FormAController@form')->name('form');
//    Route::prefix('forma')->name('forma.')->controller(FormAController::class)->group(function () {

    // フォーム A
    Route::prefix('form_a')->name('form_a.')->controller(FormAController::class)->group(function () {
        Route::get('', 'form')->name('form');
        Route::post('credit', 'credit')->name('credit');
        Route::post('check_credit', 'check_credit')->name('check_credit');
        Route::post('confirm', 'confirm')->name('confirm');
        Route::post('send', 'send')->name('send');
        Route::get('send_complete', 'send_complete')->name('send_complete');
        Route::get('credit_ok/{credit_key}', 'credit_ok')->name('credit_ok');
        Route::get('credit_ng/{credit_key}', 'credit_ng')->name('credit_ng');
        Route::post('calculate', 'calculate')->name('calculate');
        Route::get('credit_error', 'credit_error')->name('credit_error');
        Route::get('system_error', 'system_error')->name('system_error');
    });

    // フォーム B
    Route::prefix('form_b')->name('form_b.')->controller(FormBController::class)->group(function () {
        Route::get('', 'form')->name('form');
        Route::post('confirm', 'confirm')->name('confirm');
        Route::post('send', 'send')->name('send');
        Route::get('send_complete', 'send_complete')->name('send_complete');
    });

    // フォーム C
    Route::prefix('form_c')->name('form_c.')->controller(FormCController::class)->group(function () {
        Route::get('', 'form')->name('form');
        Route::post('credit', 'credit')->name('credit');
        Route::post('check_credit', 'check_credit')->name('check_credit');
        Route::post('confirm', 'confirm')->name('confirm');
        Route::post('send', 'send')->name('send');
        Route::get('send_complete', 'send_complete')->name('send_complete');
        Route::get('credit_ok/{credit_key}', 'credit_ok')->name('credit_ok');
        Route::get('credit_ng/{credit_key}', 'credit_ng')->name('credit_ng');
        Route::post('calculate', 'calculate')->name('calculate');
        Route::get('credit_error', 'credit_error')->name('credit_error');
        Route::get('system_error', 'system_error')->name('system_error');
    });

});


// 管理画面
Route::prefix('backend')->name('backend.')->group(function () {
    $options = [
        'register' => false,
        'reset'    => false,
        'confirm'  => false,
        'verify'   => false,
    ];
    Auth::routes($options);
});
Route::prefix('backend')->name('backend.')->namespace('App\Http\Controllers\Backend')->middleware('auth')->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('home', 'index')->name('home');
    });

    // フォームA
    Route::prefix('form_a')->name('form_a.')->namespace('FormA')->group(function () {
        // 申込データ管理
        Route::prefix('applications')->name('applications.')->controller(ApplicationController::class)->group(function () {
            Route::get('download_csv', 'download_csv')->name('download_csv');
        });
        Route::resource('applications', ApplicationController::class);
        // 基本・項目設定
        // 自動返信メール設定
        Route::prefix('settings')->name('settings.')->controller(SettingController::class)->group(function () {
            Route::get('', 'index')->name('index');
            Route::put('basic', 'basic')->name('basic');
            Route::put('item', 'item')->name('item');
            Route::get('replymail', 'replymail')->name('replymail');
            Route::put('replymail', 'update_replymail')->name('replymail.update');
        });
    });

    // フォームB
    Route::prefix('form_b')->name('form_b.')->namespace('FormB')->group(function () {
        // 申込データ管理
        Route::prefix('applications')->name('applications.')->controller(ApplicationController::class)->group(function () {
            Route::get('download_csv', 'download_csv')->name('download_csv');
        });
        Route::resource('applications', ApplicationController::class);
        // 基本・項目設定
        // 自動返信メール設定
        Route::prefix('settings')->name('settings.')->controller(SettingController::class)->group(function () {
            Route::get('', 'index')->name('index');
            Route::put('basic', 'basic')->name('basic');
            Route::get('replymail', 'replymail')->name('replymail');
            Route::put('replymail', 'update_replymail')->name('replymail.update');
        });
    });

    // フォームC
    Route::prefix('form_c')->name('form_c.')->namespace('FormC')->group(function () {
        // 申込データ管理
        Route::prefix('applications')->name('applications.')->controller(ApplicationController::class)->group(function () {
            Route::get('download_csv', 'download_csv')->name('download_csv');
        });
        Route::resource('applications', ApplicationController::class);
        // 基本・項目設定
        // 自動返信メール設定
        Route::prefix('settings')->name('settings.')->controller(SettingController::class)->group(function () {
            Route::get('', 'index')->name('index');
            Route::put('basic', 'basic')->name('basic');
            Route::put('item', 'item')->name('item');
            Route::get('replymail', 'replymail')->name('replymail');
            Route::put('replymail', 'update_replymail')->name('replymail.update');
        });
    });

    // メール送信設定
    Route::prefix('settings')->name('settings.')->controller(SettingController::class)->group(function () {
        Route::get('mail', 'mail')->name('mail');
        Route::put('mail', 'update_mail')->name('mail.update');
    });

    // アカウント
    Route::resource('users', 'UserController');

});
