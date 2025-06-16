<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Route;

Route::middleware(TrustProxies::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'getLogin'])->name('login');
        Route::get('/register', [AuthController::class, 'getRegister'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
        Route::post('/register', [AuthController::class, 'register'])->name('register.post');
        Route::get('/login/google', [AuthController::class, 'google'])->name('login.google');
        Route::get('/login/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('login.google.callback');
    });
    
    Route::get('/forgot-password', [AuthController::class, 'getForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password.post');

    Route::middleware('auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/edit-profile', [AuthController::class, 'getEditProfile'])->name('edit-profile');
        Route::post('/edit-profile', [AuthController::class, 'editProfile'])->name('edit-profile.post');
        Route::get('/logout', [AuthController::class, 'getLogout'])->name('logout');
        Route::post('/check-username', [AuthController::class, 'checkUsername']);

        Route::get('/contact', [ContactController::class, 'getContact'])->name('contact');
        Route::get('/add-contact', [ContactController::class, 'getAddContact'])->name('add-contact');
        Route::post('/search-contact', [ContactController::class, 'searchContact'])->name('search.contact');
        Route::post('/add-contact', [ContactController::class, 'addContact'])->name('add.contact');
        Route::post('/contact/accept', [ContactController::class, 'acceptContact'])->name('accept.contact');
        Route::post('/contact/reject', [ContactController::class, 'rejectContact'])->name('reject.contact');

        
        Route::get('/groups', [GroupController::class, 'getGroup'])->name('groups');


        Route::get('/create-group', [GroupController::class, 'getCreateGroup'])->name('create-group');
        Route::post('/create-group', [GroupController::class, 'createGroup']);
    });

    
    Route::get('/bill1', function () {
        return view('bill1');
    });

    Route::post('/bill2', [BillController::class, 'getBill2'])->name('bill2');
    Route::post('/bill3', [BillController::class, 'getBill3'])->name('bill3');
    Route::post('/create-bill', [BillController::class, 'createBill'])->name('create.bill');
    Route::get('/totalamount/{billId}', [BillController::class, 'getTotalAmount'])->name('totalamount');

    Route::post('/confirmed/{id}', [BillController::class, 'confirmBill'])->name('confirm');

    Route::post('/ocr', [BillController::class, 'getOcr'])->name('ocr');
});

Route::get('/{any}', function (){
    return redirect()->route('dashboard');
});

