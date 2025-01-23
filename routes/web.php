<?php

use App\Livewire\Dashboard\Dashboard;
use Filament\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectNotActiveUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Livewire\UserRegistration;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/register', UserRegistration::class)->name('register');
Route::get('/dashboard', Dashboard::class)->middleware([
                                                                    Authenticate::class,
                                                                    RedirectNotActiveUser::class
                                                                ])->name('dashboard');

Route::get('/send-email', function () {
    Mail::send('emails.welcome', ['nome' => 'Emerson Ferreira'], function ($message) {
        $message->to('emecatarino@yahoo.com.br');
        $message->from(config('mail.from.address'));
        $message->subject('Teste Laravel');
    });
});
