<?php

use App\Livewire\Dashboard\Dashboard;
use Filament\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectNotActiveUser;
use Illuminate\Support\Facades\Route;
use App\Livewire\UserRegistration;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/register', UserRegistration::class)->name('register');
Route::get('/dashboard', Dashboard::class)->middleware([
                                                                    Authenticate::class,
                                                                    RedirectNotActiveUser::class
                                                                ])->name('dashboard');
// Verificação de e-mail do usuário

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect(route('dashboard')); // Redireciona para o painel após a verificação
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verifique o link enviado para seu email!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

