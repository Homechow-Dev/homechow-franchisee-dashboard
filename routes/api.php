<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\KioskController;
use App\Http\Controllers\Api\MealsController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\OrdersController;
use App\Http\Controllers\API\DiscountController;
use App\Http\Controllers\API\RestockController;

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

// Route::post('/sanctum/token', function (Request $request) {
//     $request->validate([
//         'email' => 'required|email',
//         'password' => 'required',
//         'device_name' => 'required',
//     ]);
 
//     $user = User::where('email', $request->email)->first();
 
//     if (! $user || ! Hash::check($request->password, $user->password)) {
//         throw ValidationException::withMessages([
//             'email' => ['The provided credentials are incorrect.'],
//         ]);
//     }
 
//     return $user->createToken($request->device_name)->plainTextToken;
// });


Route::controller(UserAuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout');
});

Route::post('/mobile-payment-intent', [PaymentController::class, 'makePaymentIntent']);
// Customer Service application
Route::post('application', [CustomerController::class, 'franchiseeApplication']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/refresh-token', [UserAuthController::class, 'refreshToken']);
});
Route::middleware('auth:sanctum')->group(function() {
    Route::get('accounts', [AccountController::class, 'index']);
    Route::post('create/franchisee', [AccountController::class, 'createFranchisee']);
    Route::post('update/franchisee', [AccountController::class, 'updateFranchisee']);
    Route::get('accounts/{account}', [AccountController::class, 'franchiseAccount']); 
    Route::get('accounts/products/{account}', [AccountController::class, 'franchiseeProducts']);
    Route::get('accounts/profile/{account}', [AccountController::class, 'franchiseeProfile']);  

    //Kisok and meals driect Reports
    Route::get('accounts/sales/{account}', [AccountController::class, 'kioskSales']);

    // Kiosk Calls
    Route::post('create/kiosk', [KioskController::class, 'createKiosk']);
    Route::get('kiosks', [KioskController::class, 'index']);

    // Order calls
    Route::get('orders', [OrdersController::class, 'orders']);
    Route::get('order/{order}', [OrdersController::class, 'orderNumber']);

    // meal calls
    Route::get('meals', [MealsController::class, 'meals']);
    Route::post('create/meals', [MealsController::class, 'createMeals']);

    // Restock tranactions
    Route::get('restock/tranactions/{kiosk}', [RestockController::class, 'restock']);

    // Discount Franchisee request
    Route::post('create/discount', [DiscountController::class, 'createDiscount']);

    // member payment 
    Route::post('/member-payment', [PaymentController::class, 'memberPayment']);
    // wallet process
    Route::post('/wallet/addfunds', [PaymentController::class, 'userAddFunds']);
});
