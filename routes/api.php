<?php

use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->apiResource('projects', ProjectController::class);
