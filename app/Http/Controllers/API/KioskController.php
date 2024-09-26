<?php

namespace App\Http\Controllers\API;

use App\Models\Kiosk;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\LoadDelivery;
use App\OpenApi\Parameters\Kiosk\CreateKioskParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

#[OpenApi\PathItem]
class KioskController extends BaseController {

    /**
     * Retrieves all Kiosk.
     *
     * Returns all kiosk by status
     */
    #[OpenApi\Operation(tags: ['Kiosk'])]
    public function index() {
        $kiosks = Kiosk::all();
        $kiosk = $kiosks->groupBy('Status');
        $output = [
            'kioks' => $kiosk,
        ];
        return $this->sendResponse($output, 'Kiosk retrieved successfully.');  
    }


     /**
     * Retrieves all Kiosk.
     *
     * Returns all kiosk by status
     */
    #[OpenApi\Operation(tags: ['Kiosk'])]
    #[OpenApi\Parameters(factory: CreateKioskParameters::class)]
    public function createKiosk(Request $request){

        $request->validate([
            'account_id' => 'required|string|max:255',
        ]);

        $kiosk = Kiosk::create([
            'account_id' => $request->account,
            'KioskType' => $request->KioskType,
            'KioskNumber' => $request->KioskNumber,
            'TradeNO' => $request->TradeNO,
            'MachineID' => $request->MachineID,
        ]);

        $output = [
            'kioks' => $kiosk,
        ];

        return $this->sendResponse($output, 'Kiosk retrieved successfully.');  
    }

    /**
     * Update Kiosk.
     *
     * Returns Kiosks
     */
    #[OpenApi\Operation(tags: ['Kiosk'])]
    public function editKiosk(Kiosk $kiosk){

        $kl = Kiosk::find($kiosk);

        $output = [
            'kiosk' => $kl,
        ];
        return $this->sendResponse($output, 'Kiosk retrieved succesfully');
    }


     /**
     * Retrieves all Kiosk.
     *
     * Returns all kiosk by status
     */
    #[OpenApi\Operation(tags: ['Kiosk'])]
    #[OpenApi\Parameters(factory: CreateKioskParameters::class)]
    public function updateKiosk(Request $request, $id) {
        $k = $request->all();
        $kl = Kiosk::find($id);
        if($k['Account_id'] != Null){$kl->KioskType = $k['Account_id'];}
        if($k['KioskType'] != Null){$kl->KioskType = $k['KioskType'];}
        if($k['KioskNumber'] != Null){$kl->KioskType = $k['KioskNumber'];}
        if($k['KioskAddress'] != Null){$kl->KioskType = $k['KioskAddress'];}
        if($k['city'] != Null){$kl->KioskType = $k['City'];}
        if($k['State'] != Null){$kl->KioskType = $k['State'];}
        if($k['Zip'] != Null){$kl->KioskType = $k['Zip'];}
        if($k['Latitude'] != Null){$kl->KioskType = $k['Latitude'];}
        if($k['Longitude'] != Null){$kl->KioskType = $k['Longitude'];}
        if($k['Status'] != Null){$kl->KioskType = $k['Status'];}
        if($k['TotalMeals'] != Null){$kl->KioskType = $k['TotalMeals'];}
        if($k['TotalSold'] != Null){$kl->KioskType = $k['TotalSold'];}
        $kl->save();

        $output = [
            'kioks' => $kl,
        ];

        return $this->sendResponse($output, 'Kiosk updated successfully.');  
    }

    /**
     * Destroy Meal.
     *
     * Deleted meal response
     */
    #[OpenApi\Operation(tags: ['Meals'])]
    public function delete($id) {
        $kiosk = Kiosk::destroy($id);

        $output = [
            'kiosk' => 'Success',
        ];
        return $this->sendResponse($output, 'Kiosk has been deleted');
    }

    /**
     * Retrieves all Kiosk.
     *
     * Returns all kiosk by status
     */
    #[OpenApi\Operation(tags: ['FieldKiosk'])]
    public function kioskMachine(Request $request) {
        // 2.1.2.2 response mode: status, slot, ProductID, TradeNO
        $k = $request->all();
        $macID = $k['MachineID'];
        $code = $k['FunCode'];

        $machine = DB::table('machines')->where('MachineID', $macID,)
        ->where('FunCode', $code)
        ->get();

        //dd($machine);

        if( $k['FunCode'] !== '1000' && !empty($machine) ){
            // update machine with function code
            // if($k['Account_id'] != Null){$kl->KioskType = $k['Account_id'];}
            // if($k['KioskType'] != Null){$kl->KioskType = $k['KioskType'];}
            // if($k['KioskNumber'] != Null){$kl->KioskType = $k['KioskNumber'];}
            // if($k['KioskAddress'] != Null){$kl->KioskType = $k['KioskAddress'];}
            // if($k['city'] != Null){$kl->KioskType = $k['City'];}
            // if($k['State'] != Null){$kl->KioskType = $k['State'];}
            // if($k['Zip'] != Null){$kl->KioskType = $k['Zip'];}
            // if($k['Latitude'] != Null){$kl->KioskType = $k['Latitude'];}
            // if($k['Longitude'] != Null){$kl->KioskType = $k['Longitude'];}
            // if($k['Status'] != Null){$kl->KioskType = $k['Status'];}
            // if($k['TotalMeals'] != Null){$kl->KioskType = $k['TotalMeals'];}
            // if($k['TotalSold'] != Null){$kl->KioskType = $k['TotalSold'];}
            // $kl->save();

            $status = 0;
            $tradeNo = '';
            $SessionCode = '';
            $productID = '';
            $message = 'hello team yes making progress data recieved';

            return $this->machineResponse($status,$tradeNo,$SessionCode,$productID, $message);
            
        }   

        if( $k['FunCode'] === '1000') {
            // create entry to send to Load Delivery table
            $machine = LoadDelivery::create([
                'FunCode' => $request['FunCode'],
                'MachineID' => $request['MachineID'],
                'TradeNO' => $request['TradeNO'],
                'SlotNO' => $request['SlotNO'],
                'KeyNum' => $request['KeyNum'],
                'Status' => $request['Status'],
                'Quantity' => $request['Quantity'],
                'Stock' => $request['Stock'],
                'Capacity' => $request['Capacity'],
                'Price' => $request['Price'],
                'ProductID' => $request['ProductID'],
                'Type' => $request['Type'],
                'Introduction' => $request['Introduction'],
                'Name' => $request['Name'],
            ]);

            $status = 0;
            $SlotNO = '';
            $ProductID = '';
            $message = 'hello team yes making progress data recieved';

        return $this->loadResponse($status, $SlotNO, $ProductID, $message);
        }

        if( $k['FunCode'] === '4000' && !empty($machine)) {
            // create entry to send to Load Delivery table
            $machine = LoadDelivery::create([
                'FunCode' => $request['FunCode'],
                'MachineID' => $request['MachineID'],
                'TradeNO' => $request['TradeNO'],
                'SlotNO' => $request['SlotNO'],
                'KeyNum' => $request['KeyNum'],
                'Status' => $request['Status'],
                'Quantity' => $request['Quantity'],
                'Stock' => $request['Stock'],
                'Capacity' => $request['Capacity'],
                'Price' => $request['Price'],
                'ProductID' => $request['ProductID'],
                'Type' => $request['Type'],
                'Introduction' => $request['Introduction'],
                'Name' => $request['Name'],
            ]);

            $status = 0;
            $SlotNO = '';
            $ProductID = '';
            $message = 'hello team Function code 4000 yes making progress data recieved';

        return $this->loadResponse($status, $SlotNO, $ProductID, $message);
        }
    }

}
