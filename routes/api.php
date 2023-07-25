<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerHistoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Create a new bank account for a customer, with an initial deposit amount.
Route::post('/createNewUserAccount', [AccountController::class, 'createNewUserAccount'])->name('createNewUserAccount');
// Transfer amounts between any two accounts.
Route::post('/transferAmount', [AccountController::class, 'transferAmount'])->name('transferAmount');
// Retrieve balances for a given account.
Route::get('/getBalancesForAccount/{id}', [AccountController::class, 'getBalancesForAccount'])->name('getBalancesForAccount');
// Retrieve transfer history for a given account.
Route::get('/getTransferHistory/{id}', [CustomerHistoryController::class, 'getTransferHistory'])->name('getTransferHistory');
