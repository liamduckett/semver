<?php

use App\Http\Controllers\Homepage;
use Illuminate\Support\Facades\Route;

Route::get('/', Homepage::class);
