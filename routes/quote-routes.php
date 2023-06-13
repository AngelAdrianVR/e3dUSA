<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;

Route::resource('quotes', QuoteController::class);