<?php

use App\Http\Controllers\BillingSystemController;
use Composer\InstalledVersions;
use Illuminate\Support\Facades\DB;
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

Route::post('/billing-system', [BillingSystemController::class, 'index']);
Route::get('/health', function () {
    DB::connection()->getPdo();
    return response()->json([
        'version' => InstalledVersions::getRootPackage()['pretty_version'] ?? '0.0.1'
    ]);
});
