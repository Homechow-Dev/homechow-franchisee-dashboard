<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\LoadDelivery;
use App\Http\Controllers\API\BaseController as BaseController;
use App\OpenApi\Parameters\Kiosk\CreateKioskParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

class LoadDeliveryController extends BaseController
{
     /**
     * Retrieves all Kiosk.
     *
     * Returns all kiosk by status
     */
    #[OpenApi\Operation(tags: ['FieldKiosk'])]
    public function index() {
        $kiosks = LoadDelivery::all();
        // $kiosk = $kiosks->groupBy('Status');
        $output = [
            'kioks' => $kiosks,
        ];
        return $this->sendResponse($output, 'Kiosk retrieved successfully.');  
    }
}
