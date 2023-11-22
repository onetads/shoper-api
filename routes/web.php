<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
Route::group(['prefix' => 'configure', 'controller' => PageController::class], function () {
    Route::get('/', 'index')->name('configure.index');
    Route::post('/', 'save')->name('configure.save');
});
Route::view('thanks', 'onetads.thanks')->name('onetads.thanks');
