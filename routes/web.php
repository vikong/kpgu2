<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [TestController::class, 'index']);
Route::get('/process', [TestController::class, 'process']);
Route::get('/coord', [TestController::class, 'coord']);
Route::get('/coordate', [TestController::class, 'coordate']);
Route::get('/item', [TestController::class, 'item']);
Route::get('/eventitems', [TestController::class, 'eventitems']);
Route::get('/processitems', [TestController::class, 'processitems']);
Route::get('/itemhist', [TestController::class, 'itemhist']);

