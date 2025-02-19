<?php

use App\Livewire\HomePage;
use App\Livewire\AnimalHelp;
use App\Livewire\CancelPage;
use App\Livewire\SuccessPage;
use App\Livewire\BestPractices;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\Auth\ForgotPasswordPage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', HomePage::class);
Route::get('/best-practices', BestPractices::class);
Route::get('/login', LoginPage::class);
Route::get('/register', RegisterPage::class);
Route::get('/forgot', ForgotPasswordPage::class);
Route::get('/reset', ResetPasswordPage::class);
Route::get('/animal-help', AnimalHelp::class);

Route::get('/success', SuccessPage::class);
Route::get('/cancel', CancelPage::class);
