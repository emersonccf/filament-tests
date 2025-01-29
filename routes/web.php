<?php

use App\Livewire\Dashboard\Dashboard;
use App\Http\Middleware\Authenticate; //personalizado não é o do Filament\Http\Middleware\Authenticate
use App\Http\Middleware\RedirectNotActiveUser;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
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
                                                                    EnsureEmailIsVerified::class,
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
    return redirect()->route('home')->with('message', 'Verifique o link enviado para seu email!'); //back()->with('message', 'Verifique o link enviado para seu email!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Rota de fallback para login
Route::get('/login', function() {
    return redirect()->route('filament.adm.auth.login');
})->name('login');
