<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $errMessage = '';
        $errInd = 0;

        $products = DB::select('EXEC sp_getProductClassification @ERR_MESSAGE = :ERR_MESSAGE OUTPUT, @ERR_IND = :ERR_IND OUTPUT', [
            'ERR_MESSAGE' => &$errMessage,
            'ERR_IND' => &$errInd
        ]);

        if ($errInd == 1) {
            return response()->json(['error' => 'Error executing stored procedure.', 'message' => $errMessage], 500);
        }

        return response()->json(['data' => $products, 'message' => 'Product Classification retrieved successfully.'], 200);
    }
}
