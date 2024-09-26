<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Temp;
use App\Http\Controllers\API\BaseController as BaseController;
use App\OpenApi\Parameters\Kiosk\CreateKioskParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

class TempController extends BaseController
{
    /**
     * Retrieves all Kiosk.
     *
     * Returns all kiosk by status
     */
    #[OpenApi\Operation(tags: ['FieldKiosk'])]
    public function index() {
        $kiosks = Temp::all();
        // $kiosk = $kiosks->groupBy('Status');
        $output = [
            'kioks' => $kiosks,
        ];
        return $this->sendResponse($output, 'Kiosk Tempature retrieved successfully.');  
    }
}
