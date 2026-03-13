<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('admin.pages.index');
// })->name('index.get');

// admin auth
Route::get('/profile',[AuthController::class,'profilePage'])->name('profile.get');

