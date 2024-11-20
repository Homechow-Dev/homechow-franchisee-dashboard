<?php

namespace App\Http\Controllers\API;

use App\Models\Kiosk;
use App\Models\Account;
use App\Models\Meal;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\DispenseFeedback;
use App\Models\LoadDelivery;
use App\Models\Temp;
use App\OpenApi\Parameters\Kiosk\CreateKioskParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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

        $all = DB::table('kiosks')
            ->join('kiosk_meal', 'kiosks.id', '=', 'kiosk_meal.kiosk_id')
            ->select('kiosks.id', 'kiosks.KioskNumber', 'kiosks.KioskAddress', 'kiosks.Status', 'kiosks.TotalSold', 'kiosks.TotalMeals',
            'kiosk_meal.StockTotal')
            ->get();

        $Kiosk = $all->groupBy('KioskNumber');

        $currentStock = [];
        foreach( $Kiosk as $i ) {

            $Ki = $i->groupBy('KioskNumber');
            $s = $i->countBy('KioskNumber');
            
            $currentStock[] = Arr::add($Ki,'Meals_Count', $s);
        }

        $output = $currentStock;
        //$output = $Kiosk;

        return $this->sendResponse($output, 'Kiosk retrieved successfully.');  
    }

    public function kioskDetail(Kiosk $kiosk){
        $k = Kiosk::where('id', $kiosk->id)->with('meals')->get();
        
        // Return response json
        $output = $k;
        return $this->sendResponse($output, 'Kiosk detail retrieved successfully.');  
    }


     /**
     * Retrieves all Kiosk.
     *
     * Returns all kiosk by status
     */
    #[OpenApi\Operation(tags: ['Kiosk'])]
    #[OpenApi\Parameters(factory: CreateKioskParameters::class)]
    public function createKiosk(Request $request, Account $account){

        $a = $request->all();
        $acct = $account;
        foreach($a["data"] as $k ){
            $KioskNumber = "HCM_" . mt_rand(000000, 9999999);
            $kiosk = Kiosk::create([
                'Account_id' => $acct["id"],
                'KioskType' => $k["KioskType"],
                'KioskNumber' => $KioskNumber,
                'MachineID' => $k["MachineID"],
                'Status' => "Inactive",
                'KioskAddress' => $k["KioskAddress"],
                'City' => $k["City"],
                'State' => $k["State"],
                'Zip' => $k['Zip'],
            ]);
        }

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
    public function updateKiosk(Request $request, $id) {
        $k = $request->all();
        $kl = Kiosk::find($id);
        if($k['Account_id'] != Null){$kl->Account_id = $k['Account_id'];}
        if($k['KioskType'] != Null){$kl->KioskType = $k['KioskType'];}
        if($k['KioskNumber'] != Null){$kl->KioskNumber = $k['KioskNumber'];}
        if($k['KioskAddress'] != Null){$kl->KioskAddress = $k['KioskAddress'];}
        if($k['City'] != Null){$kl->City = $k['City'];}
        if($k['State'] != Null){$kl->State = $k['State'];}
        if($k['Zip'] != Null){$kl->Zip = $k['Zip'];}
        if($k['Latitude'] != Null){$kl->Latitude = $k['Latitude'];}
        if($k['Longitude'] != Null){$kl->Longitude = $k['Longitude'];}
        if($k['Status'] != Null){$kl->Status = $k['Status'];}
        if($k['MachineID'] != Null){$kl->MachineID = $k['MachineID'];}
        // if($k['TotalMeals'] != Null){$kl->KioskType = $k['TotalMeals'];}
        // if($k['TotalSold'] != Null){$kl->KioskType = $k['TotalSold'];}
        $kl->save();

        $output = [
            'kioks' => $kl,
        ];

        return $this->sendResponse($output, 'Kiosk updated successfully.');  
    }

    /**
     * Update Kiosk status.
     *
     * Status update for kiosk online or not
     */
    #[OpenApi\Operation(tags: ['Kiosk'])]
    public function statusUpdateKiosk(Request $request, Kiosk $kiosk) {
        $k = $request->all();
        DB::table('Kiosk')
        ->updateOrInsert(
            ['MachineID' => $kiosk->MachineID, 'KioskNumber' => $kiosk->KioskNumber],
            ['Status' => $k["Status"]]
        );

        $output = [
            'kiosk' => 'Success',
        ];
        return $this->sendResponse($output, 'Kiosk status has been Updated');
    }

    /**
     * Destroy Meal.
     *
     * Deleted meal response
     */
    #[OpenApi\Operation(tags: ['Kiosk'])]
    public function delete($id) {
        $kiosk = Kiosk::destroy($id);

        $output = [
            'kiosk' => 'Success',
        ];
        return $this->sendResponse($output, 'Kiosk has been deleted');
    }

    public function KioskQRPayment(Request $request) {
       
        $parameter = $request->all();
        // dd($parameter['mid']);
        // We need to check if a orders has been placed with the parameters and has been paid for with stripe.
        // if thier is a order paid for matching the parameters query load deleveries with the parameters 
        $dispsense = DB::table('load_deliveries')->where([
            ['MachineID', '=', $parameter['mid']],
            ['SlotNo', '=', $parameter['sid']], 
            ['ProductID', '=', $parameter['pid']],
            ['Price', '=', $parameter['pri']],
            ['Stock', '=', 1],
       ])->get();
    //    Else send status failed. do not dispense

        // dd('yes we have touched the correct route we can procedd');
        // dd($dispsense[0]->TradeNo);
        if(!$dispsense->isEmpty()){
            $response = Http::post('http://lab.zjznai.com/labSystem/exam/points/record/exchangeMachineService', [
                $status = 0,
                $MsgType = 0,
                $TradeNo = $dispsense[0]->TradeNo,
                $SlotNo = $dispsense[0]->SlotNo,
                $ProductID = $dispsense[0]->ProductID,
                $Err = '',
            ]);
            $status = 0;
            $MsgType = 0;
            $TradeNo = $dispsense[0]->TradeNo;
            $SlotNo = $dispsense[0]->SlotNo;
            $ProductID = $dispsense[0]->ProductID;
            $Err = '';

            return $this->deliverResponse($status, $MsgType, $TradeNo, $SlotNo, $ProductID, $Err);
        } else {
            $status = '1';
            $MsgType = '';
            $TradeNo = ' ';
            $SlotNo = ' ';
            $productID = ' ';
            $Err = '';
    
            return $this->deliverResponse($status, $MsgType, $TradeNo, $SlotNo, $productID, $Err);

        }
            
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
            ->updateOrInsert(['MachineID' => $macID, 'FunCode' => $code]);

            $status = 0;
            $TradeNo = '';
            $SlotNo = '';
            $Err = '';
            return $this->machineResponse($status,$TradeNo, $SlotNo, $Err);
            
        }

        // meal deliver/pickup from machine
        if( $code === '5000') {
            $slot = $k['SlotNo'];

            DB::table('dispense_feedback')
            ->updateOrInsert([ 'MachineID' => $macID, 'SlotNo' => $slot ],[
                'FunCode' => $code,
                'Status' => $request['Status'],
                'TradeNo' => $request['TradeNo'],
                'PayType' => $request['PayType'],
                'Time' => $request['Time'],
                'Amount' => $request['Amount'],
                'ProductID' => $request['ProductID'],
                'Name' => $request['Name'],
                'Type' => $request['Type'],
                'Quantity' => $request['Quantity'],
            ]);
            
            $status = 0;
            $TradeNo = $request['TradeNo'];
            $SlotNo = $slot;
            $Err = '';
            return $this->machineResponse($status,$TradeNo, $SlotNo, $Err);
            
        }

        if( $code === 5101 ) {
            // Creates entry for machine Tempature deletes every 3 hours
            Temp::create([
                'temp' => $request['temp'],
                'FunCode' => $request["FunCode"],
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
            $load = DB::table('load_deliveries')
                    ->updateOrInsert(['MachineID' => $macID, 'SlotNo' => $slot],[
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
            
            // Update kiosk_meals stock
            // kiosk_meal where machineId & SlotNO update or insert current stocktotal & productID

            
            $status = '0';
            $SlotNo = $slot; 
            $TradeNo = $request['TradeNo'];
            $ImageUrl = '';

        return $this->loadResponse($status, $SlotNo, $TradeNo, $ImageUrl);
        }

        if( $code === 5102 ) {
            // create entry to send to Load Delivery table
            DB::table('machines')
            ->insert(['MachineID' => $macID, 'FunCode' => $code ]);

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
            $message = '';

        return $this->loadResponse($status, $SlotNO, $ProductID, $message);
        }
    }

}
