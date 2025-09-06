<?php

use App\Http\Controllers\api\JobCompletedController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/mark-job-completed', [JobCompletedController::class, 'markAsJobCompleted'])->middleware('api-sec');
