<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/contact', [ContactController::class, 'index']);
Route::post('/contact', [ContactController::class, 'store']);
Route::post('/contact/confirm', [ContactController::class, 'confirm']);
Route::get('/contact/thanks', [ContactController::class, 'thanks']);