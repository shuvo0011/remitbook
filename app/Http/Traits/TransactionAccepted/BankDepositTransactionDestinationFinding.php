<?php

namespace App\Http\Traits\TransactionAccepted;

use Illuminate\Support\Facades\DB;
use App\Http\Traits\RoutingFind\routingNumberFind;
use App\Models\Bank_Match;
use App\Models\Bnk_Br_Match;

trait BankDepositTransactionDestinationFinding
{

    use routingNumberFind;

    public function findOutBankDepositTransactionDestination($RECEIVER_BANK, $RECEIVER_BANK_BRANCH, $RECEIVER_SUB_COUNTRY_LEVEL_2, $RECIEVER_BANK_BR_ROUTING_NUMBER, $AGENT_CODE, $trnTp)
    {



        $routingMatch = 0;
        $historydata = 0;
        $foundMatchingBankCode = 0;

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



        // ......................................... routing number check .......................................................
        if ($bnkBrRoutingNumber > 1) {
            // dd("routing found");
            // dd($bnkBrRoutingNumber);
            if (strlen($bnkBrRoutingNumber1) == 8) {
                $bnkBrRoutingNumber1 = '0' . $bnkBrRoutingNumber1;
            }
            $bnkQ =  DB::select("select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$bnkBrRoutingNumber1'");
            // dd($bnkQ);
            $result =  $this->routing($bnkQ);
            // dd($result);
            if ($result["found"] == true) {
                return $result;
            }
            $routingMatch = 1;
        }


        
        // ........................................ routing number in other column .........................................
        if ($recBnk != '' or $recBnkBr != '') {
            //  dd("routing number in other column");
            if (is_numeric($recBnk)) {
                //dd("bnk-routn");
                $bnkQ = DB::select("select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$recBnk'");
                $result =  $this->routing($bnkQ);
                dd($result);
                if ($result["found"] == true) {
                    return $result;
                    $routingMatch = 1;
                }
            } elseif (is_numeric($recBnkBr1)) {
                $bnkQ = DB::select("select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$recBnkBr1'");
                $result =  $this->routing($bnkQ);
                dd($result);
                if ($result["found"] == true) {
                    return $result;
                    $routingMatch = 1;
                }
            } elseif (is_numeric($recDist)) {
                $bnkQ = DB::select("select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$recDist'");
                $result =  $this->routing($bnkQ);
                dd($result);
                if ($result["found"] == true) {
                    return $result;
                    $routingMatch = 1;
                }
            }
            //dd("routing serc end");
        }


        // bank findout if yes branch khujben ai bank er

//    .............................  previous search from data table ....................................
        if ($routingMatch == 0) {
            //dd("sdlkfjl");
            //$agntFileAna1 = DB::select("select * from bnk_br_match where agent_code='$AGENT_CODE' and bnk_in_file='$recBnk' and br_in_file='$recBnkBr'");

            $agntFileAna1 = Bnk_Br_Match::where('agent_code',$AGENT_CODE)->where('bnk_in_file',$recBnk)->where('br_in_file',$recBnkBr)->get();

            //dd(count($agntFileAna1));
            if (count($agntFileAna1) > 0) {
                $bnkRoutingData = $agntFileAna1;
                $bankCode = $bnkRoutingData[0]->routing_no[0] . $bnkRoutingData[0]->routing_no[1] . $bnkRoutingData[0]->routing_no[2];
                $foundMatchingDist = $bnkRoutingData[0]->routing_no[3] . $bnkRoutingData[0]->routing_no[4];
                $routing = $bnkRoutingData[0]->routing_no;
                // dd($foundMatchingDist);

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
                $historydata = 1;
            }
        }


// .............................. ........ bank search .............................
        if ($routingMatch == 0 && $historydata == 0) {
            //dd("bank search ");
            $RECEIVER_BANK = strtoupper($RECEIVER_BANK);
            $RECEIVER_BANK = str_replace(" ", "", $RECEIVER_BANK); //replace space
            $RECEIVER_BANK = str_replace(",", "", $RECEIVER_BANK); //replace ,
            $RECEIVER_BANK = str_replace(".", "", $RECEIVER_BANK); //replace .
            $RECEIVER_BANK = str_replace("-", "", $RECEIVER_BANK); //replace -
            $RECEIVER_BANK = str_replace("BANK", "", $RECEIVER_BANK); //replace BANK
            $RECEIVER_BANK = str_replace("LIMITED", "", $RECEIVER_BANK); //replace limited
            $RECEIVER_BANK = str_replace("LTD", "", $RECEIVER_BANK); //replace LTD
            //$RECEIVER_BANK=str_replace("THE ", "", $RECEIVER_BANK);
            $RECEIVER_BANK = str_replace("THE", "", $RECEIVER_BANK); //replace THE

            $RECEIVER_BANK = trim($RECEIVER_BANK);
            //dd($RECEIVER_BANK);

            $d1 = DB::select("select top 1 * from bank_match where possible_bank='$RECEIVER_BANK'");
            $q1 = count($d1);
            //dd($d1);

            if ($q1 > 0) {
                $matchBankNameStr = $d1[0]->bank;
                $foundMatchingBankCode = $d1[0]->bnkKeyCode;
            } else {
                $matchBankNameStr = $RECEIVER_BANK;
                $foundMatchingBankCode = "";
            }
// ..............................................branch search ........................................................
           // dd($foundMatchingBankCode);
            if ($foundMatchingBankCode > 0) {

                $REC_BNK_BRANCH_NAME = $recBnkBr;
                $REC_BNK_BRANCH_NAME = preg_replace('/ BRANCH.*/', '', $REC_BNK_BRANCH_NAME);
                $REC_BNK_BRANCH_NAME = preg_replace('/ - /', '', $REC_BNK_BRANCH_NAME);
                $REC_BNK_BRANCH_NAME = preg_replace('/[0-9]+/', '', $REC_BNK_BRANCH_NAME);

                $bnkBrMatch = array();
                $bnkBrCodeMatch = array();
                $bnkBrMatchPercent = array();
                $bnkBrRouting = array();
                $br_type_check = 0;

                array_push($bnkBrMatchPercent, 0);
                array_push($bnkBrMatch, 0);
                array_push($bnkBrCodeMatch, 0);
                array_push($bnkBrRouting, 0);

                //dd($bnkBrCodeMatch);
                //dd($foundMatchingBankCode);
                // $v = similar_text('SIRAJGANJ', 'SERAJGANJ', $per);
                // echo $per;
                // die();

                $bnkBrList = DB::select("select * from  bnk_br_info where bankCode='$foundMatchingBankCode'");
                //dd($bnkBrList);

                foreach($bnkBrList as $data){
                    $AvailableBankBranch = strtoupper($data->branchName);
                    $AvailableBankBranch = preg_replace('/ BRANCH.*/', '', $AvailableBankBranch);

                    similar_text($REC_BNK_BRANCH_NAME, $AvailableBankBranch, $percentBnkBr);

                    array_push($bnkBrMatchPercent, $percentBnkBr);
                    array_push($bnkBrMatch, $data->branchName);
                    array_push($bnkBrCodeMatch, $data->brKeyCode);
                    array_push($bnkBrRouting, $data->routingNumber);
                }

                // echo "<pre>";
                // print_r($bnkBrMatchPercent);
                //dd($bnkBrMatchPercent);
                // dd($bnkBrMatch);
                //dd($bnkBrRouting);
                //dd(max($bnkBrMatchPercent));

                 $m = max($bnkBrMatchPercent);

                if ( $m >= 85) {
                    $bankBrMatchPercentIndexKey = array_search($m, $bnkBrMatchPercent);
                    //dd($bankBrMatchPercentIndexKey);
                    $bnkBrRoutingNumber = $bnkBrRouting[$bankBrMatchPercentIndexKey];
                    //dd($bnkBrRoutingNumber);
                    $d_br  = DB::select("select * from bnk_br_info where routingNumber='$bnkBrRoutingNumber'");
                    //dd($d_br);
                    if ($d_br[0]->routingNumber > 0) {
                        $matchBranchNameStr = strtoupper($d_br[0]->branchName);
                        $foundMatchingBankBrCode = $d_br[0]->brKeyCode;

                        $bnkBrDistSysCode = $d_br[0]->routingNumber[3] . $d_br[0]->routingNumber[4];
                        $distSysR = DB::select("select * from city where bbCodeRouting='$bnkBrDistSysCode'");
                        //dd($distSysR);
                        $distNameSys = $distSysR[0]->district_city;
                        $foundMatchingDistCode = $distSysR[0]->code;
                        return [
                            "found" => true,
                            "matchBankNameStr" => $RECEIVER_BANK,
                            "foundMatchingBankBrCode" => $foundMatchingBankBrCode,
                            "distict" => $distNameSys,
                            "matchBranchNameStr" => $matchBranchNameStr,
                            "bnkBrRoutingNumber" => $bnkBrRoutingNumber
                        ];
                    }

                } else {
                    $matchBranchNameStr = "";
                    $foundMatchingBankBrCode = 0;
                    $foundMatchingDistCode = 0;
                }
            }
        }

        return [
            "found" => false
        ];

        // log not found == failed
    }
}
