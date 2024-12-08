<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\HomechowAccounts;
use App\Notifications\NewNotification;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;
use Illuminate\Support\Str;

class HomechowAccountsController extends BaseController {
    
     /**
     * Retrieves all Homechwo Users.
     *
     * Returns Homechow employees 
     */
    #[OpenApi\Operation(tags: ['accounts'])]
    public function index() {
        $HCaccount = HomechowAccounts::select("id", 'Name', 'Email', 'Position', "Status", 'ImageUrl', 'created_at')->get();
        $output = $HCaccount;

        return $this->sendResponse($output, 'Homechow employess retrieved successfully.'); 
    }

    /**
     * Create Franchisee Account.
     *
     * request needs (Name, email, phone, Address, city, zipCode, country)
     * returns accountId for kiosk linking
     */
    #[OpenApi\Operation(tags: ['accounts'])]
    //#[OpenApi\Parameters(factory: FranchiseeAccountParameters::class)]
    public function createHomechowEmployee(Request $request) {

        $request->validate([
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'Email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
        ]);

        $fname = $request->FirstName;
        $lname = $request->LastName;

        $fullname =  Str::of($lname)->prepend($fname);

        $FirstPass = "HC_" . mt_rand(000000, 9999999);
        $user = User::create([
            'name' => $fullname,
            'email' => $request->Email,
            'password' => Hash::make($FirstPass),
        ]);

        $user->assignRole($request->Role);
        $user->notify(new NewNotification());

        $account = HomechowAccounts::create([
            'Name' => $user->name,
            'user_id' => $user->id,
            'Email' => $request->Email,
            'Phone' => $request->Phone,
            'Gender' => $request->Gender,
            'Status' => 'inactive',
            'FirstPass' => $FirstPass,
        ]);

        $acctId = $account;

        $output = [
            'account' => $acctId,
            'user' => $FirstPass,
        ];

        return $this->sendResponse($output, 'Homechow employee created successfully.'); 
    }

    /**
     * Create Franchisee Account.
     *
     * request needs (Name, email, phone, Address, city, zipCode, country)
     * returns accountId for kiosk linking
     */
    #[OpenApi\Operation(tags: ['accounts'])]
    //#[OpenApi\Parameters(factory: FranchiseeAccountParameters::class)]
    public function updateHomechowEmployee(Request $request, HomechowAccounts $homechowaccounts) {

        $request->validate([
            'Name' => 'required|string|max:255',
            'Email' => 'string|lowercase|email|max:255',
        ]);

        $id = $homechowaccounts->id;
        $account = HomechowAccounts::find($id);
        if($request->Name != ''){$account->Name = $request->Name;}
        if($request->Email != ''){$account->Email = $request->Email;}
        if($request->Phone != ''){$account->Phone = $request->Phone;}
        if($request->PresentAddress != ''){$account->PresentAddress = $request->PresentAddress;}
        if($request->PermanentAddress != ''){$account->PermanentAddress = $request->PermanentAddress;}
        if($request->City != ''){$account->City = $request->City;}
        if($request->State != ''){$account->State = $request->State;}
        if($request->Zip != ''){$account->Zip = $request->Zip;}
        if($request->Country != ''){$account->Country = $request->Country;}
        if($request->Status != ''){$account->Status = $request->Status;}
        $account->save();

        if($request->Email != '') {
            $user = User::where('id', $account->user_id)->get();
            $u = $user[0]->id;
            $changeEmail = User::find($u);
            $changeEmail->email = $request->Email;
            $changeEmail->save();
        }

        $acctId = $account;
        $output = [
            'account' => $acctId,
        ];

        return $this->sendResponse($output, 'Homechow employee updated successfully.'); 
    }

    public function updatePassword(Request $request, HomechowAccounts $homechowaccounts) {
        $acct = $homechowaccounts;
        $request->validate([           
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('id', $acct->user_id)->get();
            $u = $user[0]->id;
            $changePassword = User::find($u);
            $changePassword->password = Hash::make($request->password);
            $changePassword->save();

            $acctId = $homechowaccounts;
            $output = ['Password'];

            return $this->sendResponse($output, 'updated successfully.'); 
    }


}
