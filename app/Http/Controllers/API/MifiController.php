<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Mifi;
use App\Http\Controllers\API\BaseController as BaseController;
use App\OpenApi\Parameters\Kiosk\CreateKioskParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

class MifiController extends BaseController
{
     /**
     * Retrieves all Kiosk.
     *
     * Returns all kiosk by status
     */
    #[OpenApi\Operation(tags: ['Mifi'])]
    public function index() {
        $mifi = Mifi::get();
        $totalMifi = $mifi->count();
        $notused = Mifi::where('MachineId', 'null')->count();
        $availableMifi = $totalMifi - $notused;

        $output = [
            'mifiList' => $mifi,
            'totalMifi' => $totalMifi,
            'availableMifi' => $availableMifi,
        
        ];

        return $this->sendResponse($output, 'mifi detail retrieved successfully.');
    }

     /**
     * Create New Mifi Device.
     *
     * Returns created mifi device
     */
    #[OpenApi\Operation(tags: ['Mifi'])]
    public function createMifi(Request $request) {

        $a = $request->all(); 

        $addMifi = Mifi::create([
            'MifiId' => $a['MifiId'], 
            'Location' => $a['Location'],
            'MachineId' => $a['MachineId'],
            'SimNumber' => $a['SimNumber'],
            'Provider' => $a['Provider'],
        ]);

        $output = $addMifi->MifiId;

        return $this->sendResponse($output, 'Kiosk created successfully.');  
    }

    /**
     * Update  Mifi Device.
     *
     * Returns updated mifi device
    **/
    public function updateMifi(Request $request, $id) {
        $k = $request->all();
        $kl = Mifi::find($id);
        if($k['MifiId'] != Null ){$kl->MifiId = $k['MifiId'];}
        if($k['Location'] != Null ){$kl->Location = $k['Location'];}
        if($k['MachineId'] != Null ){$kl->MachineId = $k['MachineId'];}
        if($k['SimNumber'] != Null ){$kl->SimNumber = $k['SimNumber'];}
        if($k['Provider'] != Null ){$kl->Provider = $k['Provider'];}

        $kl->save();

        $output = $kl->MifiId;

        return $this->sendResponse($output, 'Kiosk updated successfully.');
    }
}
