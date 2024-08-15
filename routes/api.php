<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SurveyFormController;
use App\Http\Controllers\SurveyTakerController;
use App\Http\Controllers\TakerAnswerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\APIController;

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
Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::post('create-survey-form', [SurveyFormController::class, 'create']);
    Route::post('edit-form/{id}', [SurveyFormController::class, 'edit']);
    Route::get('view-form/{id}', [SurveyFormController::class, 'form']);
    Route::get('forms/{id}', [SurveyFormController::class, 'forms']);
    Route::get('taker-list/{id}', [SurveyTakerController::class, 'list']);
    Route::get('course-result/{id}', [SurveyTakerController::class, 'courseResult']);
    Route::get('form-list', [SurveyFormController::class, 'list']);
    Route::post('logout', [UserController::class, 'logout']);
});

Route::get('form/{id}', [SurveyFormController::class, 'view']);
Route::post('answer-survey/{id}', [SurveyTakerController::class, 'create']);
Route::get('survey-result/{id}', [TakerAnswerController::class, 'view']);
Route::get('all-survey-result/{id}', [TakerAnswerController::class, 'viewPerCourse']);

Route::post('login', [UserController::class, 'login']);
Route::post('signup', [UserController::class, 'signup']);

Route::get('monthlyExirationDate', [APIController::class, 'getMonthlyExirationDate']);
Route::get('yearlyExirationDate', [APIController::class, 'getYearlyExirationDate']);
Route::get('today', [APIController::class, 'today']);
