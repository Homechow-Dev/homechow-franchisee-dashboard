<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Restock;
use App\Models\Kiosk;
use App\Models\Account;
use App\Models\User;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Notifications\NewNotification;
use Vyuldashev\LaravelOpenApi\Attributes as OpenAPI;

class RestockController extends BaseController {

    /**
     * Retrieves all restock transactions.
     *
     * All restock orders with attached kiosk
     */
    #[OpenApi\Operation(tags: ['Restock'])]
    public function restock(Kiosk $kiosk){

        $restock = Restock::where('kiosk_id', $kiosk)->get();

        $output = [
            'restock' => $restock,
        ];
        return $this->sendResponse($output, 'Restock transactions retrieved successfully.');         

    }
    
    /*
     * Create Restock entry.
     *
     * Returns restock entry
     */
    #[OpenApi\Operation(tags: ['Restock'])]
    //#[OpenApi\Parameters(factory: CreateMealsParameters::class)]
    public function createRestock(Request $request, Kiosk $kiosk) {
    
        $kl = Kiosk::find($kiosk);

        $request->validate([
            'kioskNumber' => 'required|string|max:150',
            'quantity' => 'required|string|max:10',
        ]);
        
        $restockID = "RHC_" . mt_rand(000000, 9999999);
        $rk = Restock::create([
            'kiosk_id' => $kl[0]['id'],
            'kioskName' => $request->kioskNumber,
            'qty' => $request->quantity,
            'deliverName' => $request->deliverName,
            'restockID' => $restockID,
            'mealName' => 'chicken, Beef, Salmon',
            'category' => 'all',
            'status' => 'prepared',
        ]);
        
        $account = Account::find($kl[0]['Account_id']);
        $user = User::find($account->user_id);
        $user->notify(new NewNotification());
    
        $output = [
            'Restock' => $request->kioskNumber,
        ];
        return $this->sendResponse($output, 'restock shippment has been prepared');
    }

    public function compledteRestock(Request $request, Restock $restock){

        $request->validate([
            'kioskNumber' => 'required|string|max:150',
            'status' => 'required|string|max:10',
        ]);

        $id = $restock->id;
        $s = Restock::find($id);
        $s->status = $request->status;
        $s->save();

        $kiosk = Kiosk::where('id', $s->kiosk_id)->get();
        $account = Account::find($kiosk[0]['Account_id']);
        $user = User::find($account->user_id);
        $user->notify(new NewNotification());

        $output = $request->kioskNumber;
        return $this->sendResponse($output, 'has benn successfully restocked.');
        
    }
    
}
