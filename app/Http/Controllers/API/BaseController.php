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
    public function machineResponse($status,$tradeNo,$SessionCode,$productID, $message)
    {
    	$response = [
            'Status' => $status,
            'TradeNo' => $tradeNo,
            'SessionCode' => $SessionCode,
            'ProductID' => $productID,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function loadResponse($status,$SlotNO, $ProductID, $message)
    {
    	$response = [
            'Status' => $status,
            'SlotNO' => $SlotNO,
            'ProductID' => $ProductID,
            'message' => $message,
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
