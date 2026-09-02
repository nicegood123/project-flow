<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/login', 'Auth/Login')->middleware('guest')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::inertia('/register', 'Auth/Register')->middleware('guest')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::inertia('/projects', 'Projects/Index')->name('projects.page');
Route::inertia('/projects/create', 'Projects/Create')->name('projects.create');
Route::get('/projects/{project}/edit', fn (int $project) => Inertia::render('Projects/Edit', [
    'projectId' => $project,
]))->whereNumber('project')->name('projects.edit');
