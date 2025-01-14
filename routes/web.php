<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\UserRegistration;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', UserRegistration::class)->name('register');
