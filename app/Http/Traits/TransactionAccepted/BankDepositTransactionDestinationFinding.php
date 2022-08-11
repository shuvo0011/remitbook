<?php

namespace App\Http\Traits\TransactionAccepted;

use Illuminate\Support\Facades\DB;
use App\Http\Traits\RoutingFind\routingNumberFind;

trait BankDepositTransactionDestinationFinding
{

    use routingNumberFind;

    public function findOutBankDepositTransactionDestination($RECEIVER_BANK, $RECEIVER_BANK_BRANCH, $RECEIVER_SUB_COUNTRY_LEVEL_2, $RECIEVER_BANK_BR_ROUTING_NUMBER, $AGENT_CODE, $trnTp)
    {



        $routingMatch = 0;

        // routing 

        $recBnkBr = strtoupper($RECEIVER_BANK_BRANCH);

        $recBnkBr1 = $recBnkBr;
        if (strlen($recBnkBr1) == 8)
            $recBnkBr1 = "0" . $recBnkBr1;

        $recBnk = strtoupper($RECEIVER_BANK);
        if (strlen($recBnk) == 8)
            $recBnk = "0" . $recBnk;

        $recDist = strtoupper($RECEIVER_SUB_COUNTRY_LEVEL_2);

        $bnkBrRoutingNumber = trim($RECIEVER_BANK_BR_ROUTING_NUMBER, '&nbsp;');
        $bnkBrRoutingNumber = trim($bnkBrRoutingNumber, ' ');
        $bnkBrRoutingNumber1 = trim($bnkBrRoutingNumber, ' ');

        $foundMatchingBankCode=0;
// ......................................... routing number check .......................................................
        if ($bnkBrRoutingNumber > 1 ) {

           // dd($bnkBrRoutingNumber);

            if (strlen($bnkBrRoutingNumber1) == 8) {
                $bnkBrRoutingNumber1 = '0' . $bnkBrRoutingNumber1;
            }

            $bnkQ =  DB::select("select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$bnkBrRoutingNumber1'");
           
           // dd($bnkQ);
            $result =  $this->routing($bnkQ);
           // dd($result);
            if($result["found"]==true){
                return $result;
            }

        } 
// ........................................ routing number in other column .........................................
        if ($recBnk != '' or $recBnkBr != '') {
            //dd("dlkjf");
            if(is_numeric($recBnk)){
                //dd("bnk-routn");
                $bnkQ = DB::select("select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$recBnk'");
                $result =  $this->routing($bnkQ);
                // dd($result);
                 if($result["found"]==true){
                     return $result;
                 }
            }
            elseif(is_numeric($recBnkBr1)){
                $bnkQ = DB::select("select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$recBnkBr1'");
                $result =  $this->routing($bnkQ);
                // dd($result);
                 if($result["found"]==true){
                     return $result;
                 }
            }
            elseif(is_numeric($recDist)){
                $bnkQ = DB::select("select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$recDist'"); 
                $result =  $this->routing($bnkQ);
                // dd($result);
                 if($result["found"]==true){
                     return $result;
                 }
            }

        }

        // bank findout if yes branch khujben ai bank er
        

        if($routingMatch == 0){
            // dd("sdlkfjl");
            $agntFileAna1 = DB::select( "select * from bnk_br_match where agent_code='$AGENT_CODE' and bnk_in_file='$recBnk' and br_in_file='$recBnkBr'");
           // dd($agntFileAna1);
            if (count($agntFileAna1) > 0) {
                $bnkRoutingData = $agntFileAna1;
                $bankCode = $bnkRoutingData[0]->routing_no[0] . $bnkRoutingData[0]->routing_no[1] . $bnkRoutingData[0]->routing_no[2];
                $foundMatchingDist = $bnkRoutingData[0]->routing_no[3] . $bnkRoutingData[0]->routing_no[4];
                $routing = $bnkRoutingData[0]->routing_no;

                //dd($foundMatchingDist);

                $distSysR = DB::select("select * from city where bbCodeRouting='$foundMatchingDist'");
                
               // dd($distSysR);

                $distNameSys = $distSysR[0]->district_city;
                $foundMatchingDistCode = $distSysR[0]->code;
                $bnkR = DB::select("select * from bnk_br_info br left join bnk_info bnk on br.bankCode=bnk.bnkKeyCode where br.routingNumber='$routing'");
               
                //dd($bnkR);

                $foundMatchingBankCode = $bnkR[0]->bnkKeyCode;
                $matchBankNameStr = $bnkR[0]->bank;
                $foundMatchingBankBrCode = $bnkR[0]->brKeyCode;
                $matchBranchNameStr = strtoupper($bnkR[0]->branchName);
                $bnkBrRoutingNumber = $bnkR[0]->routingNumber;

                return [

                    "found" => true,
                    "foundMatchingBankCode" => $bnkR[0]->bnkKeyCode,
                    "matchBankNameStr" => $bnkR[0]->bank,
                    "foundMatchingBankBrCode" => $bnkR[0]->brKeyCode,
                    "matchBranchNameStr" => strtoupper($bnkR[0]->branchName),
                    "bnkBrRoutingNumber" => $bnkR[0]->routingNumber
                ];
            }

        }





        

        // log not found == failed


    }

}
