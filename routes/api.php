<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/leads', [App\Http\Controllers\Api\LeadController::class, 'index']);
Route::post('/leads', [App\Http\Controllers\Api\LeadController::class, 'create']);
Route::get('/leads/{lead}', [App\Http\Controllers\Api\LeadController::class, 'show']);
Route::put('/leads/{lead}', [App\Http\Controllers\Api\LeadController::class, 'update']);
Route::delete('/leads/{lead}', [App\Http\Controllers\Api\LeadController::class, 'destroy']);
