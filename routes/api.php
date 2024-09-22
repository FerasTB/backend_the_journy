<?php
use App\Http\Controllers\Api\SummaryController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\ResponsibilityController;
use App\Http\Controllers\Api\CVController;
use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Http\Request;
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    // Summary
    Route::get('/summaries', [SummaryController::class, 'index']);
    Route::post('/summaries', [SummaryController::class, 'store']);
    Route::get('/summaries/{id}', [SummaryController::class, 'show']);
    Route::put('/summaries/{id}', [SummaryController::class, 'update']);
    Route::delete('/summaries/{id}', [SummaryController::class, 'destroy']);

    // Skills
    Route::get('/skills', [SkillController::class, 'index']);
    Route::post('/skills', [SkillController::class, 'store']);
    Route::get('/skills/{id}', [SkillController::class, 'show']);
    Route::put('/skills/{id}', [SkillController::class, 'update']);
    Route::delete('/skills/{id}', [SkillController::class, 'destroy']);

    // references
    Route::get('/references', [ReferenceController::class, 'index']);
    Route::post('/references', [ReferenceController::class, 'store']);
    Route::get('/references/{id}', [ReferenceController::class, 'show']);
    Route::put('/references/{id}', [ReferenceController::class, 'update']);
    Route::delete('/references/{id}', [ReferenceController::class, 'destroy']);
    
    // Certificates
    Route::get('/certificates', [CertificateController::class, 'index']);
    Route::post('/certificates', [CertificateController::class, 'store']);
    Route::get('/certificates/{id}', [CertificateController::class, 'show']);
    Route::put('/certificates/{id}', [CertificateController::class, 'update']);
    Route::delete('/certificates/{id}', [CertificateController::class, 'destroy']);

    // Education
    Route::get('/education', [EducationController::class, 'index']);
    Route::post('/education', [EducationController::class, 'store']);
    Route::get('/education/{id}', [EducationController::class, 'show']);
    Route::put('/education/{id}', [EducationController::class, 'update']);
    Route::delete('/education/{id}', [EducationController::class, 'destroy']);

    // Languages
    Route::get('/languages', [LanguageController::class, 'index']);
    Route::post('/languages', [LanguageController::class, 'store']);
    Route::get('/languages/{id}', [LanguageController::class, 'show']);
    Route::put('/languages/{id}', [LanguageController::class, 'update']);
    Route::delete('/languages/{id}', [LanguageController::class, 'destroy']);

    // Experiences
    Route::get('/experiences', [ExperienceController::class, 'index']);
    Route::post('/experiences', [ExperienceController::class, 'store']);
    Route::get('/experiences/{id}', [ExperienceController::class, 'show']);
    Route::put('/experiences/{id}', [ExperienceController::class, 'update']);
    Route::delete('/experiences/{id}', [ExperienceController::class, 'destroy']);

    // Responsibilities
    Route::get('/responsibilities', [ResponsibilityController::class, 'index']);
    Route::post('/responsibilities', [ResponsibilityController::class, 'store']);
    Route::get('/responsibilities/{id}', [ResponsibilityController::class, 'show']);
    Route::put('/responsibilities/{id}', [ResponsibilityController::class, 'update']);
    Route::delete('/responsibilities/{id}', [ResponsibilityController::class, 'destroy']);

    // cvs
    Route::get('/cv', [CVController::class, 'index']);
    Route::post('/cv', [CVController::class, 'store']);
    Route::get('/cv/{id}', [CVController::class, 'show']);
    Route::put('/cv/{id}', [CVController::class, 'update']);
    Route::delete('/cv/{id}', [CVController::class, 'destroy']);
    Route::get('/cvs', [CVController::class, 'get_CV']);

});
