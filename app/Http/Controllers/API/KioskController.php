<?php

namespace App\Http\Controllers\API;

use App\Models\Kiosk;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\LoadDelivery;
use App\Models\Temp;
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

        // $machine = DB::table('machines')->where('MachineID', $macID,)
        // ->where('FunCode', $code)
        // ->get();


        if( $code === '4000') {
            DB::table('machines')
            ->updateOrInsert(['MachineID' => $macID, 'FunCode' => $code ]);

            $status = 0;
            $tradeNo = '';
            $SessionCode = '';
            $productID = '';
            $message = 'hello team yes making progress data recieved';

            return $this->machineResponse($status,$tradeNo,$SessionCode,$productID, $message);
            
        }

        if( $code === '5000') {
            $slot = $k['SlotNo'];

            DB::table('dispense_feedback')
            ->updateOrInsert(['MachineID' => $macID, 'SlotNo' => $slot ], [
                    'FunCode' => $code,
                    'TradeNo' => $request['TradeNo'],
                    "PayType" => $request['PayType'],
                    "Time" => $request['Time'],
                    "Amount" => $request['Amount'],
                    'ProductID' => $request['ProductID'],
                    'Type' => $request['Type'],
                    'Introduction' => $request['Introduction'],
                    'Name' => $request['Name'],
                    "Quantity" =>$request['Quantity'],
                    "Status" => $request['Status'],
            ]);

            $status = 0;
            $tradeNo = '';
            $SessionCode = '';
            $productID = '';
            $message = 'hello team yes making progress data recieved';

            return $this->machineResponse($status,$tradeNo,$SessionCode,$productID, $message);
            
        }

        if( $code === 5101 ) {
            // Creates entry for machine Tempature deletes every 3 hours
            Temp::create([
                'temp' => $request['temp'],
                'TradeNO' => $request["FunCode"],
                'MachineID' => $request['MachineID'],
            ]);

            $status = 0;
            $tradeNo = '';
            $SessionCode = '';
            $productID = '';
            $message = 'function code 5101 Temp data recieved';

            return $this->machineResponse($status,$tradeNo,$SessionCode,$productID, $message);
        }

        // if($code === '2000') {
        //     $a = Machine::create([
        //         'FunCode' => $request['FunCode'],
        //         'MachineID' => $request['MachineID'],
        //     ]);
            

        //     $status = 0;
        //     $tradeNo = '';
        //     $SessionCode = '';
        //     $productID = '';
        //     $message = 'function code 5102 data recieved';

        //     return $this->machineResponse($status,$tradeNo,$SessionCode,$productID, $message);
        // }

        if( $code === '1000' ) {
            // create entry to send to Load Delivery table
            $slot = $k['SlotNo'];
            DB::table('load_deliveries')
                    ->updateOrInsert(['MachineID' => $macID, 'SlotNo' => $slot ],[
                    'FunCode' => $code,
                    'TradeNo' => $request['TradeNo'],
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
                    'LockGoodsCount' => $request['LockGoodsCount']
                
                ]);

            $status = 0;
            $SlotNO = '';
            $ProductID = '';
            $message = 'function code 1000 data recieved';

        return $this->loadResponse($status, $SlotNO, $ProductID, $message);
        }

        if( $code === 5102 ) {
            // create entry to send to Load Delivery table
            DB::table('machines')
            ->updateOrInsert(['MachineID' => $macID, 'FunCode' => $code ]);

            //$machine = Machine::create([
                //         'FunCode' => $request['FunCode'],
                //         'MachineID' => $request['MachineID'],
                //         //'Coil_id' => $request['CoilList["Coil_id"]'],
                //         // "Content" => $request['CoilList["Content"]'],
                //         // "EnableDiscount" => $request['CoilList["EnableDiscount"]'],
                //         // "EnableExpire" => $request['CoilList["EnableExpire"]'],
                //         // "EnableHot" => $request['CoilList["EnableHot"]'],
                //         // "Extant_quantity" => $request['CoilList["Extant_quantity"]'],           
                //         // "Img_url" => $request['CoilList["Img_url"]'],
                //         // "LockGoodsCount" => $request['CoilList["LockGoodsCount"]'],
                //         // "Par_name" => $request['CoilList["Par_name"]'],
                //         // "Par_price" => $request['CoilList["Par_price"]'],
                //         // "Sale_price" => $request['CoilList["Sale_price"]'],
                //         // "Work_status" => $request['CoilList["Work_status'],
                //         // "dSaleAmount" => $request['CoilList["dSaleAmount"]'],
                //         // "discountRate" => $request['CoilList["discountRate"]'],
                //         // "iExpireTimeStamp" => $request['CoilList["iExpireTimeStamp"]'],
                //         // "iKeyNum" => $request['CoilList["iKeyNum"]'],
                //         // "iSaleNum" => $request['CoilList["iSaleNum"]'],
                //         // "iSlotOrder" => $request['CoilList["iSlotOrder"]'],
                //         // "iSlot_status" => $request['CoilList["iSlot_status"]'],
                //         // "iVerifyAge" => $request['CoilList["iVerifyAge"]'],
                //         // "isInventory" => $request['CoilList["isInventory"]'],
                //         // "m_AdUrl" => $request['CoilList["m_AdUrl"]'],
                //         // "m_Goods_details_url" => $request['CoilList["m_Goods_details_url"]'],
                //         // "m_QrPayUrl" => $request['CoilList["m_QrPayUrl"]'],
                //         // "m_iBack" => $request['CoilList["m_iBack"]'],
                //         // "m_iCloseStatus" => $request['CoilList["m_iCloseStatus"]'],
                //         // "m_iCol" => $request['CoilList["m_iCol"]'],
                //         // "m_iHeatTime" => $request['CoilList["m_iHeatTime"]'],
                //         // "m_iRow" => $request['CoilList["m_iRow"]'],
                //         // "m_iSlt_hvgs" => $request['CoilList["m_iSlt_hvgs"]'],
                //         // "m_strType" => $request['CoilList["m_strType"]'],
                //         // "ray" => $request['CoilList["ray"]'],
                //         // "sGoodsCapacity" => $request['CoilList["sGoodsCapacity"]'],
                //         // "sGoodsSpec" => $request['CoilList["sGoodsSpec"]'],
                //         // "strGoodsCode" => $request['CoilList["strGoodsCode"]'],
                //         // "strKeys" => $request['CoilList["strKeys"]'],
                //         // "strOtherParam1" => $request['CoilList["strOtherParam1"]'],
                //         // "strOtherParam2" => $request['CoilList["strOtherParam2"]'],
                //     ]);

            $status = 0;
            $SlotNO = '';
            $ProductID = '';
            $message = 'hello team Function code 5102 yes making progress data recieved';

        return $this->loadResponse($status, $SlotNO, $ProductID, $message);
        }
    }

}
