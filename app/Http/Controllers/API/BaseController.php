<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function machineResponse($status,$tradeNo,$SessionCode,$productID)
    {
    	$response = [
            'Status' => $status,
            'TradeNo' => $tradeNo,
            'SessionCode' => $SessionCode,
            'ProductID' => $productID,
        ];

        return response()->json($response, 200);
    }

    /**
     * deliver success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function deliverResponse($status, $MsgType, $TradeNo,$SlotNo,$productID, $Err,)
    {
    	$response = [
            'Status' => $status,
            'MsgType' => $MsgType,
            'TradeNo' => $TradeNo,
            'SlotNo' => $SlotNo,
            'ProductID' => $productID,
            '$Err' => $Err,
        ];

        return response()->json($response, 200);
    }

    public function loadResponse($status,$SlotNo, $TradeNo, $ImageUrl )
    {
    	$response = [
            'Status' => $status,
            'SlotNo' => $SlotNo,
            'TradeNo' => $TradeNo,
            'ImageUrl ' => $ImageUrl,
            // 'ImageDetailUrl' => $ImageDetailUrl,
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
