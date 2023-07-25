<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CustomerHistoryController extends Controller
{
    /**
     * Get the history of transfers (to and from) for an account.
     * @param string $id
     * @return JsonResponse
     */
    public function getTransferHistory(int $id): JsonResponse
    {
        if(empty($id)){
            return Response::json('Id field is required', 404);;
        }
        $returnArray = [];
        $historyRecords = DB::table('customer_histories')->get();
        foreach ($historyRecords as $historyRecord) {
            if(
                ($historyRecord->from_account === $id || $historyRecord->to_account === $id) && 
                strpos("Transfered", $historyRecord->action) >= 0
            ) {  
                $returnArray[] = $historyRecord;
            }
        }
        if(!empty($returnArray)) {
            return Response::json($returnArray, 200);
        }
        return Response::json('No Records were found for account id # ' . $id, 404);
    }
}
