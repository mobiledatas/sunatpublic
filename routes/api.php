<?php

use App\Http\Controllers\ApiLoginController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\SharepointController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Thybag\SharePointAPI;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('/v1')->group(function(){
    Route::post('/token',[ApiLoginController::class,'login']);
    Route::middleware('auth:api')->group(function(){
        Route::resource('/bill',BillController::class);
        Route::post('/note',[CreditNoteController::class,'store']);
        Route::get('/documentpdf/{document}',[BillController::class,'downloadpdf']);
        Route::get('/documentxml/{document}',[BillController::class,'downloadxml']);
    });
    // Route::get('/sp',function ()
    // {
    //     $spc = new SharepointController();
    //     $recurrentes =  $spc->getRecurrentes();
    //     foreach ($recurrentes as $recurrente) {
    //         print_r("\n-----------------------------");
    //         $items = $spc->getItems($recurrente->getProperty('ID'));
    //         print_r($items);
    //     }
    //     return response()->json($recurrentes);
    // });
});
