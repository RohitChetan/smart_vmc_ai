<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

Route::get('/', function () {
    return redirect()->route('citizen.home');
});

Route::get('/citizen', function () {
    return view('citizen.home');
})->name('citizen.home');

Route::get('/citizen/login', function () {
    return view('citizen.auth.login');
})->name('citizen.login');

Route::get('/citizen/register', function () {
    return view('citizen.auth.register');
})->name('citizen.register');

Route::get('/citizen/complaints/create', function () {
    return view('citizen.complaints.create');
})->name('citizen.complaints.create');

Route::get('/citizen/track', function () {
    return view('citizen.track');
})->name('citizen.track');

Route::get('/field/login', function () {
    return view('field.login');
})->name('field.login');

Route::get('/field/dashboard', function () {
    return view('field.dashboard');
})->name('field.dashboard');

Route::get('/admin/login', function () {
    return view('admin.auth.login');
})->name('admin.login');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/master', function () {
    return view('admin.master.index');
});

Route::get('/admin/settings', function () {
    return view('admin.settings.index');
})->name('admin.settings');