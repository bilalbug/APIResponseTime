<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiResponseController;
use App\Http\Controllers\OfficeIpAddressController;
use App\Http\Controllers\EmployeesAttendanceRecordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//get data from mock API
Route::get('/time', [ApiResponseController::class, 'APIResponseUserTimeLocation']);

//this is for storing record in DB
Route::get('/records/all', [EmployeesAttendanceRecordController::class, 'index']);

//office IP Show/Store/Update/Delete
Route::get('/ip', [OfficeIpAddressController::class, 'index']);
Route::post('/ip', [OfficeIpAddressController::class, 'store']);
Route::get('/ip/{ipAddress}', [OfficeIpAddressController::class, 'show']);
Route::put('/ip/{ipAddress}', [OfficeIpAddressController::class, 'update']);
Route::delete('/ip/{ipAddress}', [OfficeIpAddressController::class, 'destroy']);


