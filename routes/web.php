<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::post('/check', Controller::class);
