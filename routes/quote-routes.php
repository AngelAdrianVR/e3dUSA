<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;

Route::resource('quotes', QuoteController::class);
Route::post('quotes/massive-delete', [QuoteController::class, 'massiveDelete'])->name('quotes.massive-delete');
Route::post('quotes/clone', [QuoteController::class, 'clone'])->name('quotes.clone');
Route::post('quotes/create-so', [QuoteController::class, 'createSO'])->name('quotes.create-so');