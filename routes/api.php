<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\{
    UserAuthController,
    AccountController,
    CustomerController,
    KioskController,
    MealsController,
    PaymentController,
    OrdersController,
    DiscountController,
    RestockController,
    ChargesController,
    DispenseFeedbackController,
    FeedbackController,
    CategoryController,
    FaqController,

};

use App\Http\Controllers\{
    LoginController,
    LogoutController,
    RegisterController,
    UserController
};


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

// Auth ...
Route::post('/login', LoginController::class);
Route::post('/register', RegisterController::class);
Route::post('/logout', LogoutController::class);


// mobile login
Route::controller(UserAuthController::class)->group(function(){
    Route::post('mobile/register', 'register');
    Route::post('mobile/login', 'login');
    Route::post('mobile/logout', 'logout');
});

//  User....
Route::get('/user', UserController::class)->middleware(['auth:sanctum']);

// KIOSK MACHINE FUNCTION CODE ROUTES
Route::post('machine', [KioskController::class, 'kioskMachine']);
Route::post('application', [CustomerController::class, 'franchiseeApplication']);

// STRIPE CONSUMER APP PAYMENT
Route::post('/mobile-payment-intent', [PaymentController::class, 'makePaymentIntent']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/refresh-token', [UserAuthController::class, 'refreshToken']);
});

Route::middleware('auth:sanctum', 'verified')->group(function() {
    // ADMINISTRATION PANEL ROUTES HOMECHOW EMPLOYEES
    Route::prefix('V1')->group(function () {
        Route::controller(AccountController::class)->group(function () {
            Route::get('accounts', 'index');
            Route::get('franchisee/profile/{account}', 'franchiseAccountProfile');
            Route::post('create/franchisee', 'createFranchisee');
        });

        Route::controller(MealsController::class)->group(function () {
            Route::get('meals', 'meals');
            Route::post('create/meals', 'createMeals');
            Route::post('update/meals/{id}', 'updateMeals');
            Route::post('update/meal/status/{meal}', 'statusUpdateMeal');
            Route::get('delete/meals/{id}', 'delete');
        });

        Route::controller(KioskController::class)->group(function () {
            Route::get('kiosks', 'index');
            Route::post('create/kiosk/{account}', 'createKiosk'); 
            Route::post('update/kiosks/{id}', 'updateKiosk');
            Route::post('update/kiosk/status/{kiosk}', 'statusUpdateKiosk');
            Route::get('kiosk/detail/{kiosk}',  'kioskDetail');
            Route::get('delete/kiosks/{id}', 'delete');
        });

        Route::controller(ChargesController::class)->group(function () {
            Route::get('charges/stripe', 'updateCustomer');
        });

        Route::controller(CategoryController::class)->group(function (){
            Route::get('categories/list', 'index');
        });

    });

    // MOBILE API FOR FRANCHISEE APP AND CONSUMER APP
    Route::prefix('mobileV1')->group(function () {

    });
    // Franchise account data for admin panel and Mobile
    Route::post('update/franchisee/{account}', [AccountController::class, 'updateFranchisee']);
    Route::get('accounts/{account}', [AccountController::class, 'franchiseAccount']); 
    Route::get('accounts/products/{account}', [AccountController::class, 'franchiseeProducts']);

    Route::post('update/email/{account}', [AccountController::class, 'updateEmail']);
    Route::post('update/pin/{account}', [AccountController::class, 'updateAccountPin']);
    Route::post('confirmation/pin/{account}', [AccountController::class, 'verifyPin']);

    //Kisok and meals driect Reports
    Route::get('accounts/sales/{account}', [AccountController::class, 'kioskSales']);

    // Order calls
    Route::get('orders', [OrdersController::class, 'orders']);
    Route::get('order/{order}', [OrdersController::class, 'orderNumber']);
    Route::get('order/topmeals', [OrdersController::class, 'topMeals']);

    // faq
    Route::get('faq/list', [FaqController::class, 'index']);
    Route::post('faq/create', [FaqController::class, 'createFaq']);
    Route::post('faq/update', [FaqController::class, 'updateFaq']);
    Route::get('faq/delete', [FaqController::class, 'deleteFaq']);

    // Reports
    Route::get('reports/orders/{account}', [OrdersController::class, 'orderReports']);

    // Franchisee Feedback
    Route::get('feedback', [FeedbackController::class, 'allFeedback']);
    Route::post('submit/feedback/{account}', [FeedbackController::class, 'feedbackFranchisee']);
    Route::get('feedback/{account}', [FeedbackController::class, 'accountUserFeedback']);
    Route::get('feedback/delete/{account}', [FeedbackController::class, 'deleteFeedback']);


    // Restock tranactions
    Route::get('restock/tranactions/{kiosk}', [RestockController::class, 'restock']);
    Route::post('create/restock/{kiosk}', [RestockController::class, 'createRestock']);

    // Discount Franchisee request
    Route::post('create/discount', [DiscountController::class, 'createDiscount']);

    // member payment 
    Route::post('/member-payment', [PaymentController::class, 'memberPayment']);
    Route::get('/onboarding/account/{account}', [PaymentController::class, 'expressAccount']);
    
    // wallet process
    Route::post('/wallet/addfunds', [PaymentController::class, 'userAddFunds']);

});
