<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Restock;
use App\Models\Kiosk;
use App\Http\Controllers\API\BaseController as BaseController;
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
            'kioskName' => 'required|string|max:150',
            'quantity' => 'required|string|max:10',
        ]);

        
        $rk = Restock::create([
            'kiosk_id' => $kl['id'],
            'machineID' => $kl['machineID'],
            'kioskName' => $request->kioskName,
            'quantity' => $request->qty,
            'deliverName' => $request->deliverName,
            'status' => 'prepared',
        ]);
        
        $output = [
            'Restock' => $rk,
        ];
        return $this->sendResponse($output, 'Restock successfully saved.');
    }

    //delete restock transaction

    
}
