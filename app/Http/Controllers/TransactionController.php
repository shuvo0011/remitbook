<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionValid;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    

    // public function __construct()
    // {
    //     // $this->middleware('auth:api', ['except' => ['login']]);
            // TransactionValid $request

    // }


    public function transac_accept(TransactionValid $request){



        $uploadDate = $request->date;
        $sAgent = $request->agent_code;
        $in_key = $request->tag_name;
        $bankQuery = "";


        //dd($request->all());

        $valid = $request->validated();

        if ($in_key != '') {
            $sql_q = "select * from [transaction] where uploadDate='$uploadDate' and AGENT_CODE='$sAgent' and stLevel='0' and uploadTag='$in_key' $bankQuery";
            $sql_qBankList = "select RECEIVER_BANK from [transaction] where AGENT_CODE='$sAgent' and stLevel='0' and uploadTag='$in_key'  and uploadDate='$uploadDate' group by RECEIVER_BANK order by RECEIVER_BANK";
         
            $q = DB::select($sql_q);
            // dd($q);
            $qBankList = DB::select($sql_qBankList);
            
        } else {
            $sql_q = "select * from [transaction] where AGENT_CODE='$sAgent' and stLevel='0' and uploadDate='$uploadDate' $bankQuery";
            $sql_qBankList = "select RECEIVER_BANK from [transaction] where AGENT_CODE='$sAgent' and stLevel='0'  group by RECEIVER_BANK order by RECEIVER_BANK";
            $q = DB::select($sql_q);
            $qBankList = DB::select($sql_qBankList);
        }


        return TransactionResource::collection($q);




        // return response()->json([
        //     "status" => 200,
        //     "success" => true,
        //     "message" => "all bank list",
        //     "data" => $q,
        // ]);


    }

}
