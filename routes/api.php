<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\API\{
    UserAuthController,
    AccountController,
    ApplicationController,
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
    MifiController,

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
Route::post('application', [CustomerController::class, 'franchiseeApplication']);

// STRIPE CONSUMER APP PAYMENT
Route::post('/mobile-payment-intent', [PaymentController::class, 'makePaymentIntent']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/refresh-token', [UserAuthController::class, 'refreshToken']);
});

// MOBILE API FOR FRANCHISEE APP AND CONSUMER APP
Route::prefix('mobileV1')->group(function () {
    Route::controller(PaymentController::class)->group(function (){
        Route::post('/onboarding/account/{account}', 'expressAccount');
        // Route::get('/onboarding/update/{account}', 'expressAccountUpdate');
        Route::post('stripe/reauth', 'expressAccountReturnUrl');
        // Route::get('/onboarding/return', 'expressAccountUpdate');
    });
});

// KIOSK MACHINE FUNCTION COde ROUTES ========================>
Route::post('machine', [KioskController::class, 'kioskMachine']);
Route::get('qrcode/release', [KioskController::class, 'KioskQRPayment']);

// KIOSK MACHINE FUNCTION CODE ROUTES ========================>


Route::middleware('auth:sanctum', 'verified')->group(function() {
    // ADMINISTRATION PANEL ROUTES HOMECHOW EMPLOYEES
    Route::prefix('V1')->group(function () {
        Route::controller(AccountController::class)->group(function () {
            Route::get('accounts', 'index');
            Route::get('accounts/{account}', 'franchiseAccount'); 
            Route::get('franchisee/profile/{account}', 'franchiseAccountProfile');
            Route::post('create/franchisee', 'createFranchisee');
        });

        Route::controller(MealsController::class)->group(function () {
            Route::get('meals', 'meals');
            Route::post('create/meals', 'createMeals');
            Route::post('update/meals/{id}', 'updateMeals');
            Route::post('update/meal/status/{meal}', 'statusUpdateMeal');
            Route::get('meals/list/today', 'mealsListToday');
            Route::post('meals/list/bydate', 'mealsListBydate');
            Route::get('delete/meals/{id}', 'delete');
        });

        Route::controller(KioskController::class)->group(function () {
            Route::get('kiosks', 'index');
            Route::post('create/kiosk/{account}', 'createKiosk'); 
            Route::post('update/kiosks/{id}', 'updateKiosk');
            Route::post('update/kiosk/status/{kiosk}', 'statusUpdateKiosk');
            Route::get('info/kiosk/{kiosk}',  'kioskInformation');
            Route::get('kiosk/meals/today', 'kitchenDataToday');
            Route::post('kiosk/meals/bydate', 'kioskByDate');
            Route::post('kiosk/detail/cat/{kiosk_id}',  'kioskCategoryRank');
            Route::get('delete/kiosks/{id}', 'delete');
        });

        Route::controller(ChargesController::class)->group(function () {
            Route::get('charges/stripe', 'updateCustomer');
        });

        Route::controller(CategoryController::class)->group(function (){
            Route::get('categories/list', 'index');
        });

        Route::controller(ApplicationController::class)->group(function (){
            Route::get('applications', 'applicationPage');
            Route::post('application/status/{id}', 'applicationStatus');
        });

        Route::controller(MifiController::class)->group(function (){
            Route::get('mifis', 'index');
            Route::post('mifis/create', 'createMifi');
            Route::post('mifis/update/{id}', 'updateMifi');
        });

        

        Route::get('all/transaction/orders', [OrdersController::class, 'allOrdersTransactions']);

        Route::get('stripe/account/payouts', [PaymentController::class, 'allAccountPayouts']);

    });
    // Mobile account management
    Route::prefix('FranchiseeV1')->group(function () {
        Route::controller(AccountController::class)->group(function () { 
            Route::post('update/franchisee/{account}', 'updateFranchisee');
            Route::get('accounts/{account}', 'mobileFranchiseAccount'); 
            Route::get('accounts/products/{account}', 'franchiseeProducts');
            Route::post('update/email/{account}', 'updateEmail');
            Route::post('update/pin/{account}', 'updateAccountPin');
            Route::post('update/phone/{account}','updatePhone');
            Route::post('update/password/{account}', 'updatePassword');
            Route::post('confirmation/pin/{account}', 'verifyPin');
        });

        Route::post('stripe/account/data', [PaymentController::class, 'franchiseeAccountData']);

     });

    // Franchise account data for admin panel and Mobile
    // Route::post('update/franchisee/{account}', [AccountController::class, 'updateFranchisee']);
    // Rmove this route before turning application live
    Route::get('accounts/{account}', [AccountController::class, 'franchiseAccount']); 

    //Kisok and meals driect Reports
    Route::get('accounts/sales/{account}', [AccountController::class, 'kioskSales']);

    // Order calls
    Route::get('orders', [OrdersController::class, 'orders']);
    Route::get('order/{order}', [OrdersController::class, 'orderNumber']);
    Route::get('order/topmeals', [OrdersController::class, 'topMeals']);
    Route::get('reports/orders/{account}', [OrdersController::class, 'orderReports']);

    // faq
    Route::get('faq/list', [FaqController::class, 'index']);
    Route::post('faq/create', [FaqController::class, 'createFaq']);
    Route::post('faq/update', [FaqController::class, 'updateFaq']);
    Route::get('faq/delete', [FaqController::class, 'deleteFaq']);


    // Franchisee Feedback
    Route::get('feedback', [FeedbackController::class, 'allFeedback']);
    Route::post('submit/feedback/{account}', [FeedbackController::class, 'feedbackFranchisee']);
    Route::get('feedback/{account}', [FeedbackController::class, 'accountUserFeedback']);
    Route::get('feedback/delete/{account}', [FeedbackController::class, 'deleteFeedback']);
    // Need to create Franchisee Application submittals 


    // Restock tranactions
    Route::get('restock/tranactions/{kiosk}', [RestockController::class, 'restock']);
    Route::post('create/restock/{kiosk}', [RestockController::class, 'createRestock']);

    // Discount Franchisee request
    Route::post('create/discount', [DiscountController::class, 'createDiscount']);

    // member payment 
    Route::post('/member-payment', [PaymentController::class, 'memberPayment']);
    
    
    // wallet process
    Route::post('/wallet/addfunds', [PaymentController::class, 'userAddFunds']);

});
