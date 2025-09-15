<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TelegramBotController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('telegram_bots', TelegramBotController::class);
});
