<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\RegistrationController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\UserController;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // Auth (Firebase)
    Route::post('auth/firebase-login',    [AuthController::class, 'firebaseLogin']);
    Route::post('auth/firebase-register', [AuthController::class, 'firebaseRegister']);
    Route::post('auth/logout',            [AuthController::class, 'logout']);

    // Events (public read)
    Route::get('events',          [EventController::class, 'index']);
    Route::get('events/{event}',  [EventController::class, 'show']);

    // Categories (public read)
    Route::get('categories',            [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);

    // ─── Protected Routes (Firebase Token required) ──────────────────────────
    Route::middleware('firebase.auth')->group(function () {

        // Dashboard (admin)
        Route::get('dashboard/stats', [DashboardController::class, 'stats'])
            ->middleware('admin');

        // Events CRUD (admin)
        Route::post('events',                    [EventController::class, 'store'])->middleware('admin');
        Route::post('events/{event}/update',     [EventController::class, 'update'])->middleware('admin');
        Route::delete('events/{event}',          [EventController::class, 'destroy'])->middleware('admin');

        // Categories CRUD (admin)
        Route::post('categories',               [CategoryController::class, 'store'])->middleware('admin');
        Route::put('categories/{category}',     [CategoryController::class, 'update'])->middleware('admin');
        Route::delete('categories/{category}',  [CategoryController::class, 'destroy'])->middleware('admin');

        // Registrations
        Route::get('registrations/my',            [RegistrationController::class, 'myRegistrations']);
        Route::get('registrations/check/{event}', [RegistrationController::class, 'check']);
        Route::post('registrations/{event}',      [RegistrationController::class, 'store']);
        Route::delete('registrations/{event}',    [RegistrationController::class, 'destroy']);
        Route::get('registrations',               [RegistrationController::class, 'index'])->middleware('admin');

        // User profile
        Route::get('user/profile',          [UserController::class, 'profile']);
        Route::post('user/profile',         [UserController::class, 'updateProfile']);
        Route::post('user/change-password', [UserController::class, 'changePassword']);
    });
});