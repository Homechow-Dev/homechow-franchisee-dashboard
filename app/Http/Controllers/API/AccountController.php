<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\Kiosk;
use App\Models\Order;
use App\Models\User;
use App\OpenApi\Parameters\Accounts\FranchiseeAccountParameters;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;


#[OpenApi\PathItem]
class AccountController extends BaseController {

    /**
     * Retrieves all Account Users.
     *
     * Returns Franchisee and Customer members and guest grouped by Member Type
     */
    #[OpenApi\Operation(tags: ['accounts'])]
    public function index() {
        $account = Account::select("id", 'Name', "CompanyName","CompanyAddress", "Status", 'Type', 'CustomerId', 'KioskCount', 'created_at')->get();
        $output = $account;

        return $this->sendResponse($output, 'Orders retrieved successfully.'); 
    }

    /**
     * Create Franchisee Account.
     *
     * request needs (Name, email, phone, Address, city, zipCode, country)
     * returns accountId for kiosk linking
     */
    #[OpenApi\Operation(tags: ['accounts'])]
    #[OpenApi\Parameters(factory: FranchiseeAccountParameters::class)]
    public function createFranchisee(Request $request) {

        $request->validate([
            'Name' => 'required|string|max:255',
            'Email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
        ]);

        $FirstPass = "HC_" . mt_rand(000000, 9999999);
        $user = User::create([
            'name' => $request->Name,
            'email' => $request->Email,
            'password' => Hash::make($FirstPass),
        ]);

         //Generate random customer Id
        function generateCustomerId() {
            $cust = "HC_" . mt_rand(000000, 9999999);

            if (customerIdExists($cust)) {
                return generateCustomerId();
            }
            return $cust;
        }

        function customerIdExists($cust) {
            // query the database and return a boolean
            // for instance, it might look like this in Laravel
            return Account::where('CustomerId', $cust)->exists();
        }
        //Generate random customer Id

        $GCCustomerId = generateCustomerId();

        $account = Account::create([
            'Name' => $request->Name,
            'user_id' => $user->id,
            'Phone' => $request->Phone,
            'CompanyName' => $request->CompanyName,
            'CompanyAddress' => $request->CompanyAddress,
            'WalletAmount' => '0',
            'City' => $request->City,
            'State' => $request->State,
            'Zip' => $request->Zip,
            'Status' => 'inactive',
            'type' => 'franchisee',
            'Email' => $request->Email,
            'KioskCount' => '0',
            'Country' => $request->Country,
            'PresentAddress' => $request->PresentAddress, 
            'Gender' => $request->Gender,
            'CustomerId' => $FirstPass,
            'FirstPass' => $FirstPass,
        ]);

        $acctId = $account;

        $output = [
            'account' => $acctId,
            'user' => $FirstPass,
        ];

        return $this->sendResponse($output, 'Orders retrieved successfully.'); 
    }

    /**
     * Update Franchisee Account.
     *
     * request needs (Name, email, phone, Address, city, zipCode, country)
     * returns accountId for kiosk linking
     */
    #[OpenApi\Operation(tags: ['accounts'])]
    #[OpenApi\Parameters(factory: FranchiseeAccountParameters::class)]
    public function updateFranchisee(Request $request, Account $account) {
        
        $request->validate([
            'email' => 'string|lowercase|email|max:255',
            'name' => 'string|max:150',
            'phone' => 'string|max:150',
            'image' => 'mimes:jpg,bmp,png|max:250',
        ]);
        $id = $account->id;
        $account = Account::find($id);
        if($request->name != ''){$account->Name = $request->name;}
        if($request->phone != ''){$account->Phone = $request->phone;}
        if($request->email != ''){$account->Email = $request->email;}
        if($request->image != ''){$account->Image = $request->image;}
        if($request->pin != ''){$account->Pin = $request->pin;}
        $account->save();

        if($request->email != '') {
            $user = User::where('id', $account->user_id)->get();
            $u = $user[0]->id;
            $changeEmail = User::find($u);
            $changeEmail->email = $request->email;
            $changeEmail->save();
        }

        $acctId = $account;
        $output = [
            'name' => $acctId->Name,
            'email' => $acctId->email,
            'phone' => $acctId->Phone,
            'image' => $acctId->image,
            'pin' => $acctId->pin,
        ];

        return $this->sendResponse($output, 'Account update successfully'); 
    }

     /**
     * Retrieves franchisee Account user.
     *
     * Account will equal account id & 
     * Returns Franchisee account with Kiosk, Kiosk Orders, and available meals
     */
    // Kiosk::factory()->hasOrders(5)->hasMeals(6)->create();
   #[OpenApi\Operation(tags: ['accounts'])]
    public function franchiseAccount(Account $account) {
        $acct = $account;
        $useraccount = Account::where('id', $acct->id)->get();
        $ordersK = Kiosk::with('orders')->with('meals')->where('account_id', $account->id)->get();
        $countMeals = Order::where('account_id', $acct->id)->get();
            if($countMeals->isEmpty()){
                $TransactionTotal = '0';
                $TopSelling = '0';
            } else {
                $TransactionTotal = $countMeals->count();
                $TopSelling = $countMeals->countBy('MealName');
            }
            $output = [
                'account' => AccountResource::collection($useraccount),
                'kiosk' => $ordersK,
                'TopSelling' => $TopSelling,
                'TransactionTotal' => $TransactionTotal,
            ];
        // to count and group ordres
        
        return $this->sendResponse($output, 'Franchisee Account retrieved successfully.');
    }

     /**
     * Retrieves franchisee Account Profile.
     *
     * Account will equal account id & 
     * Returns Franchisee account with Kiosk Total, and Earnings
     */
    // Kiosk::factory()->hasOrders(5)->hasMeals(6)->create();
   #[OpenApi\Operation(tags: ['accounts'])]
   public function franchiseAccountProfile(Account $account) {
       $acct = $account;
       $useraccount = Account::where('id', $acct->id)->get();
       $ordersK = Kiosk::select( 
        'KioskType', 
        'KioskNumber', 
        'MealsSold', 
        'Earnings', 
        'Latitude',
        'Longitude',
        'created_at')->get();
       
       $countMeals = Order::where('account_id', $acct->id)->get();
           if($countMeals->isEmpty()){
               $TransactionTotal = '0';
               $TopSelling = '0';
           } else {
               $TransactionTotal = $countMeals->count();
               $TopSelling = $countMeals->countBy('MealName');
           }
           
        $TotalEarnings = $TransactionTotal * 2.50;
        $KioskCount = $ordersK->count();
        $output = [
            'account' => AccountResource::collection($useraccount),
            'kiosk' => $ordersK,
            'KioskCount' => $KioskCount,
            'TotalMealsSold' => $TransactionTotal,
            'TotalEarnings' => $TotalEarnings,
        ];
       // to count and group ordres
       
       return $this->sendResponse($output, 'Franchisee Profile Account retrieved successfully.');
   }

    /**
     * Retrieves franchisee Kiosk Products.
     *
     * Account will equal account id & 
     * Returns Franchisee Kiosk Products / Meals
     */
    #[OpenApi\Operation(tags: ['accounts'])]
    public function franchiseeProducts(Account $account) {
        $acct = $account;
        $useraccount = Account::where('id', $account)->get();
        $Kiosk = Kiosk::with('meals')->where('account_id', $acct->id)->get();
        $output = [
            'Products' => $Kiosk,
        ];
        return $this->sendResponse($output, 'Franchisee Account retrieved successfully.');
    }

    // kiosk total sales by day, 7day, month, all
    public function kioskSales(Account $account) {
        $acct = $account;
        // all kiosk associated
        $all = DB::table('kiosks')->where("Account_id", $acct)
            ->join('orders', 'kiosks.id', '=', 'orders.kiosk_id')
            ->select('kiosks.KioskNumber', 'kiosks.Account_id', 'orders.Account_id', 'orders.id', 'orders.OrderNumber', 'orders.MealName', 'orders.Category','orders.Amount', 'orders.ProductID', 'orders.Quantity', 'orders.created_at')
            ->get();

        $Kiosk = $all->groupBy('KioskNumber');


        $TopSellingCategory = [];
        foreach( $Kiosk as $i ){
            $a = $i->groupBy('KioskNumber');
            // $TopSellingCategory[] = $i->groupBy('Category');
            $MealsRanking = $i->countBy('Category');
            $TopSellingCategory[] = Arr::add(['Kiosk' => $a, 'topcategories' => null], 'topcategories', $MealsRanking);
        }
        
        // $TopSellingCategory = [];
        $SalesByDate = [];
        foreach( $Kiosk as $i ){
            $SalesByDate[] = $i->groupBy('created_at');
            $MealsRanking = $i->countBy('Category');
            $SalesByDate[] = Arr::add(['topcategories' => null], 'topcategories', $MealsRanking);//change is made here use array to store all values 
        }

      

        $output = [
            'SalesByKiosk' => $Kiosk,
            'TopSellingKioskCategory' => $TopSellingCategory,
            'SalesByDate' => $SalesByDate,
            // 'MealsRanking' => $MealsRanking,

        ];
        return $this->sendResponse($output, 'Franchisee Account retrieved successfully.');
    }

     /**
     * Update Franchisee Account.
     *
     * request needs (Name, email, phone, Address, city, zipCode, country)
     * returns accountId for kiosk linking
     */
    #[OpenApi\Operation(tags: ['accounts'])]
    #[OpenApi\Parameters(factory: FranchiseeAccountParameters::class)]
    public function updateEmail(Request $request, Account $account) {
        
        $request->validate([
            'email' => 'string|email|max:255',
            'pin' => 'string|max:6',
        ]);
        $id = $account->id;
        $account = Account::find($id);
        if($request->pin === $account->Pin){

            $account->Email = $request->email;
            $account->save();

            $user = User::where('id', $account->user_id)->get();
            $u = $user[0]->id;
            $changeEmail = User::find($u);
            $changeEmail->email = $request->email;
            $changeEmail->save();

            $acctId = $account;
            $output = [
                'name' => $acctId->Name,
                'email' => $acctId->Email,
                'pin' => $acctId->Pin,
            ];

            return $this->sendResponse($output, 'Email updated successfully.'); 
        } else {
            $output = [
                'email' => 'Email has not been change',
            ];
            return $this->sendResponse($output, 'denied'); 
        }
    }


    public function updateAccountPin(Request $request, Account $account) {
        $acct  = $account;
        $request->validate([
            'pin' => 'required|string|max:6',
            'newPin' => 'required|string|max:6',
            'pinConfirmation' => 'required|string|max:6'
        ]);
        $oldpin = $acct->Pin;   
        $pinConfirmation = $request->pinConfirmation;

        if( $oldpin === $request->pin) {
            
            $pinInsert = Account::find($acct->id); 
            $pinInsert->Pin = $request->newPin;
            $pinInsert->save();

            $output = [
                'pin' => $pinInsert->Pin,
            ];
    
            return $this->sendResponse($output, 'Pin created successfully.');
       
        } else {

            $output = [
                'pin' => 'Error',
            ];
    
            return $this->sendResponse($output, 'Confirmation does not match');
        }
    }

    public function updatePhone(Request $request, Account $account) {
        $acct  = $account;
        $request->validate([
            'pin' => 'required|string|max:6',
            'phone' => 'required|string|max:20'
        ]);
        $pin =  $request->pin;

        if( $pin === $acct->Pin) {
            $phoneUpdate = Account::find($acct->id); 
            $phoneUpdate->Phone = $request->phone;
            $phoneUpdate->save();

            $output = [
                'phone' => $request->phone,
            ];
    
            return $this->sendResponse($output, 'Phone created successfully.');
       
        } else {

            $output = [
                'pin' => 'Error',
            ];
    
            return $this->sendResponse($output, 'Confirmation does not match');
        }
    }

    public function updatePassword(Request $request, Account $account) {
        $acct = $account;
        $request->validate([           
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $pin =  $request->pin;

        if($pin === $acct->Pin) {
            $user = User::where('id', $acct->user_id)->get();
            $u = $user[0]->id;
            $changePassword = User::find($u);
            $changePassword->password = Hash::make($request->password);
            $changePassword->save();

            $acctId = $account;
            $output = [
                'pin' => $acctId->Pin,
            ];

            return $this->sendResponse($output, 'Password updated successfully.'); 
        } else {
            $output = [
                'password' => 'Password has not been change',
            ];
            return $this->sendResponse($output, 'Password has not been change'); 
        }
    }

    public function verifyPin(Request $request, Account $account) {
        $acct  = $account;
        $request->validate([
            'pin' => 'required|string|max:6',
        ]);
        $newpin =  $request->pin;
        $pinaccount = Account::where('id', $acct->id)->get();

        if( $newpin === $pinaccount[0]['pin']) {
            $output = [
                'pin' => 'Confirmend',
            ];
            return $this->sendResponse($output, 'Pin Confirmned successfully.');       
        } else {

            $output = [
                'pin' => 'Error',
            ];

            return $this->sendResponse($output, 'Confirmation does not match');
        }

        $output = [
            'pin' => $pinaccount,
        ];

        return $this->sendResponse($output, 'Pin retrieved successfully.');
    }

}
