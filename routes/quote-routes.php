<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;

Route::resource('quotes', QuoteController::class)->middleware('auth');
Route::post('quotes/massive-delete', [QuoteController::class, 'massiveDelete'])->name('quotes.massive-delete');
Route::post('quotes/clone', [QuoteController::class, 'clone'])->name('quotes.clone');
Route::post('quotes/create-so', [QuoteController::class, 'createSO'])->name('quotes.create-so');
Route::put('quotes/authorize/{quote}', [QuoteController::class, 'authorizeQuote'])->name('quotes.authorize');
Route::post('quotes/get-matches', [QuoteController::class, 'getMatches'])->name('quotes.get-matches');
Route::get('quotes/fetch-catalog-products-company-branch/{company_branch_id}', [QuoteController::class, 'fetchCatalogProductsCompanyBranch'])->name('quotes.fetch-catalog-products-company-branch');
Route::post('quotes/schedule-update-product-price', [QuoteController::class, 'scheduleUpdateProductPrice'])->name('quotes.schedule-update-product-price');
