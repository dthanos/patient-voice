<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TranscriptionController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (){
    Route::post('check', [AuthController::class, 'check']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('patient', PatientController::class);

    Route::match(['options', 'head', 'post', 'patch', 'delete'],'upload', [UploadController::class, 'index']);

    Route::prefix('voice_analysis')->group(function () {
        Route::post('transcribe', [TranscriptionController::class, 'transcribe']);
        Route::post('summarize', [TranscriptionController::class, 'summarize']);
    });
});
