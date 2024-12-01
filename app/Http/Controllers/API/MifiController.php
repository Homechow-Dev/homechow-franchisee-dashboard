<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Mifi;
use App\Http\Controllers\API\BaseController as BaseController;

class MifiController extends BaseController
{
    public function index() {
        $mifi = Mifi::get();
        $totalMifi = $mifi->count();
        $notused = Mifi::where('kiosk_id', 'null')->count();
        $availableMifi = $totalMifi - $notused;

        $output = [
            'mifiList' => $mifi,
            'totalMifi' => $totalMifi,
            'availableMifi' => $availableMifi,
        
        ];

        return $this->sendResponse($output, 'mifi detail retrieved successfully.');
    }

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

    public function updateMifi(Request $request, $id) {
        $k = $request->all();
        $kl = Mifi::find($id);
        if($k['MifiId'] != Null){$kl->MifiId = $k['MifiId'];}
        if($k['Location'] != Null){$kl->Location = $k['Location'];}
        if($k['MachineId'] != Null){$kl->MachineId = $k['MachineId'];}
        if($k['SimNumber'] != Null){$kl->SimNumber = $k['SimNumber'];}
        if($k['Provider'] != Null){$kl->Provider = $k['Provider'];}

        $kl->save();

        $output = $kl->MifiId;

        return $this->sendResponse($output, 'Kiosk updated successfully.');
    }
}
