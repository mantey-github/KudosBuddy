<?php
use App\Http\Controllers\Api\BotController;
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
Route::get('/', function () {
    return response()->json([
        'app_name' => 'KudosBuddy',
        'api_version' => '1.0.0',
    ]);
});

Route::post('/slack/listen', [BotController::class, 'listen']);