<?php

namespace App\Http\Traits\RoutingFind;

use Illuminate\Support\Facades\DB;

trait routingNumberFind
{

    public function routing($bnkQ)
    {

        //dd($bnkQ);
        $foundMatchingBankCode = $bnkQ[0]->bnkKeyCode;
        $matchBankNameStr = $bnkQ[0]->bank;

        $matchBranchNameStr = strtoupper($bnkQ[0]->branchName);
        $foundMatchingBankBrCode = $bnkQ[0]->brKeyCode;

        $bnkBrDistSysCode = $bnkQ[0]->routingNumber[3] . $bnkQ[0]->routingNumber[4];

        //dd($bnkBrDistSysCode);

        $distSysR = DB::select("select * from city where bbCodeRouting='$bnkBrDistSysCode'");

        $distNameSys = $distSysR[0]->district_city;
        $foundMatchingDistCode = $distSysR[0]->code;

        $bnkBrRoutingNumber = $bnkQ[0]->routingNumber;
        $routingMatch = $bnkQ[0]->routingNumber;

        if($routingMatch == '' ){
            return false;
        }
        else{
            return [
                "found" => true,
                "distNameSys" =>  $distNameSys,
                "foundMatchingBankBrCode" => $foundMatchingBankBrCode,
                "bnkBrRoutingNumber" => $bnkBrRoutingNumber
            ];
        }

    }
}
