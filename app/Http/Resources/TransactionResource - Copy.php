<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'RECEIVER_BANK' => $this->RECEIVER_BANK,
            'RECEIVER_BANK_BRANCH' => $this->RECEIVER_BANK_BRANCH,
            'RECEIVER_SUB_COUNTRY_LEVEL_2' => $this->RECEIVER_SUB_COUNTRY_LEVEL_2,
            'RECIEVER_BANK_BR_ROUTING_NUMBER' => $this->RECIEVER_BANK_BR_ROUTING_NUMBER,
            'AGENT_CODE' => $this->AGENT_CODE,
            'trnKeyCode' => $this->trnKeyCode,
            'TRN_DATE' => $this->TRN_DATE,
            'AMOUNT' => $this->AMOUNT,
            'SENDER_NAME' => $this->SENDER_NAME,
            'trnTp' => $this->trnTp,
            "transaction_destination" => $this->findTransactionDestination($this->RECEIVER_BANK, $this->RECEIVER_BANK_BRANCH, $this->RECEIVER_SUB_COUNTRY_LEVEL_2, $this->RECIEVER_BANK_BR_ROUTING_NUMBER, $this->AGENT_CODE, $this->trnKeyCode, $this->TRN_DATE, $this->AMOUNT, $this->SENDER_NAME, $this->trnTp, $this->TRANSACTION_PIN, $this->RECEIVER_ACCOUNT_NUMBER)
        ];
    }



    public function findTransactionDestination($RECEIVER_BANK, $RECEIVER_BANK_BRANCH, $RECEIVER_SUB_COUNTRY_LEVEL_2, $RECIEVER_BANK_BR_ROUTING_NUMBER, $AGENT_CODE, $trnKeyCode, $TRN_DATE, $AMOUNT, $SENDER_NAME, $trnTp, $TRANSACTION_PIN, $RECEIVER_ACCOUNT_NUMBER)
    {

        $tdCol = "";


        $stop_q = "select * from transaction_stop_payment_instrt where trnKeyCode='$trnKeyCode' and instrStatus='1'";
        $q = DB::select($stop_q);


        // if ($q){
        //     continue;
        // }

        // update transaction PIN & Other parameter...................................................

        $pin = $TRANSACTION_PIN;

        // dd($pin);

        if ($pin == '&nbsp;') {
            $pin = '';
            $tr_key_cd = $trnKeyCode;
            DB::select("update [transaction] set TRANSACTION_PIN='' where trnKeyCode='$tr_key_cd'");
        }

        $trnDateCheck = trim($TRN_DATE, '&nbsp;');
        $trnDateCheck = trim($trnDateCheck, ' ');

        // dd($trnDateCheck);


        if ($trnDateCheck == NULL) {
            $tr_key_cd = $trnKeyCode;
            DB::select("update [transaction] set TRN_DATE=uploadDate where trnKeyCode='$tr_key_cd'");
        }


        // update transaction PIN & Other parameter...................................................

        $foundMatchingBankCode = 0;
        $matchBankNameStr = "";

        $distNameFound = "";
        $foundMatchingDistCode = 0;

        $matchBranchNameStr = "";
        $foundMatchingBankBrCode = 0;
        $distNameSys = '';

        $analyticsMatch = 0;
        $routingMatch = 0;
        $history_match = 0;

        $rowCol = "";

        $recBnkBr = strtoupper($RECEIVER_BANK_BRANCH);


        // dd($recBnkBr);

        $recBnkBr1 = strtoupper($RECEIVER_BANK_BRANCH);
        if (strlen($recBnkBr1) == 8)
            $recBnkBr1 = "0" . $recBnkBr1;

        // dd($recBnkBr1);

        $recBnk = strtoupper($RECEIVER_BANK);
        if (strlen($recBnk) == 8)
            $recBnk = "0" . $recBnk;
        $recDist = strtoupper($RECEIVER_SUB_COUNTRY_LEVEL_2);

        //    dd($recBnk);



        $bnkBrRoutingNumber = trim($RECIEVER_BANK_BR_ROUTING_NUMBER, '&nbsp;');
        $bnkBrRoutingNumber = trim($bnkBrRoutingNumber, ' ');
        $bnkBrRoutingNumber1 = trim($bnkBrRoutingNumber, ' ');


        //    dd($bnkBrRoutingNumber1);


        //if routing number remain in bank cell or branch cell...........................................................................................

     
        if ($recBnk != '' or $recBnkBr != '') {

            // dd($recBnk);

            if (is_numeric($recBnk)) {

                $bnkQ = "select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$recBnk'";
                $bnkR = DB::select($bnkQ);

                // dd($bnkR);
                // die();

                $foundMatchingBankCode = $bnkR[0]->bnkKeyCode;
                $matchBankNameStr = $bnkR[0]->bank;

                $matchBranchNameStr = strtoupper($bnkR[0]->branchName);
                $foundMatchingBankBrCode = $bnkR[0]->brKeyCode;

                $bnkBrDistSysCode = $bnkR[0]->routingNumber[3] . $bnkR[0]->routingNumber[4];

                $distQ = "select * from city where bbCodeRouting='$bnkBrDistSysCode'";
                $distSysR = DB::select($distQ);

                $distNameSys = $distSysR[0]->district_city;
                $foundMatchingDistCode = $distSysR[0]->code;

                $bnkBrRoutingNumber = $bnkR[0]->routingNumber;
                $routingMatch = $bnkR[0]->routingNumber;
            } else if (is_numeric($recBnkBr1)) {

                $bnkQ = "select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$recBnkBr1'";
                $bnkR = DB::select($bnkQ);

                // dd($bnkR);
                // die();

                $foundMatchingBankCode = $bnkR[0]->bnkKeyCode;
                $matchBankNameStr = $bnkR[0]->bank;

                $matchBranchNameStr = strtoupper($bnkR[01]->branchName);
                $foundMatchingBankBrCode = $bnkR[0]->brKeyCode;

                $bnkBrDistSysCode = $bnkR[0]->routingNumber[3] . $bnkR[0]->routingNumber[4];

                $distQ = "select * from city where bbCodeRouting='$bnkBrDistSysCode'";
                $distSysR = DB::select($distQ);

                $distNameSys = $distSysR[0]->district_city;
                $foundMatchingDistCode = $distSysR[0]->code;

                $bnkBrRoutingNumber = $bnkR[0]->routingNumber;
                $routingMatch = $bnkR[0]->routingNumber;
            } else if (is_numeric($recDist)) {

                $bnkQ = "select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$recDist'";
                $bnkR = DB::select($bnkQ);

                // dd($bnkR);
                // die();

                $foundMatchingBankCode = $bnkR[0]->bnkKeyCode;
                $matchBankNameStr = $bnkR[0]->bank;

                $matchBranchNameStr = strtoupper($bnkR[0]->branchName);
                $foundMatchingBankBrCode = $bnkR[0]->brKeyCode;

                $bnkBrDistSysCode = $bnkR[0]->routingNumber[3] . $bnkR[0]->routingNumber[4];

                $distQ = "select * from city where bbCodeRouting='$bnkBrDistSysCode'";
                $distSysR = DB::select($distQ);

                $distNameSys = $distSysR[0]->district_city;
                $foundMatchingDistCode = $distSysR[0]->code;

                $bnkBrRoutingNumber = $bnkR[0]->routingNumber;
                $routingMatch = $bnkR[0]->routingNumber;
            } else {
                $routingMatch = 0;

                //dd($recBnk);
                //  dd($routingMatch);

            }
        }






        // if routing number found..................................................................................................................................................................

        if ($bnkBrRoutingNumber1 > 0 and $routingMatch == 0) {

            //    dd($routingMatch);

            // dd($bnkBrRoutingNumber1);

            if (strlen($bnkBrRoutingNumber1) == 8) {
                $bnkBrRoutingNumber1 = '0' . $bnkBrRoutingNumber1;
            }


            $bnkQ = "select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.routingNumber='$bnkBrRoutingNumber1'";
            $bnkR = DB::select($bnkQ);

            //dd($bnkR);

            //    dd($bnkR[0]->bnkKeyCode);
            //    echo $bnkR->bnkKeyCode;

            $foundMatchingBankCode = $bnkR[0]->bnkKeyCode;
            $matchBankNameStr = $bnkR[0]->bank;

            // dd($foundMatchingBankBrCode);
            //  dd($matchBankNameStr);

            $matchBranchNameStr = strtoupper($bnkR[0]->branchName);
            $foundMatchingBankBrCode = $bnkR[0]->brKeyCode;

            // dd($bnkR[0]->routingNumber);

            $bnkBrDistSysCode = $bnkR[0]->routingNumber[3] . $bnkR[0]->routingNumber[4];

            // dd($bnkBrDistSysCode);

            $distQ = "select * from city where bbCodeRouting='$bnkBrDistSysCode'";
            $distSysR = DB::select($distQ);

            //dd($distSysR);

            $distNameSys = $distSysR[0]->district_city;
            $foundMatchingDistCode = $distSysR[0]->code;

            $bnkBrRoutingNumber = $bnkR[0]->routingNumber;
            $routingMatch = $bnkR[0]->routingNumber;

            //    dd($routingMatch);
            //    dd($bnkBrRoutingNumber);

        }

        // Check Bank Name.....................................................................................................................................................

        //checking history data................................................................................


        if ($routingMatch == 0 or $routingMatch == '') {

            $ag_code = $AGENT_CODE;
            $rec_bnk = $RECEIVER_BANK;
            $rec_bnk_br = $RECEIVER_BANK_BRANCH;
            $rec_sub_2 = $RECEIVER_SUB_COUNTRY_LEVEL_2;

            //dd($ag_code);
            //dd($rec_bnk);
            //dd($rec_bnk_br);
            // dd($rec_sub_2);


            $agntFileAna1 = "select * from bnk_br_match where agent_code='$ag_code' and bnk_in_file='$rec_bnk' and br_in_file='$rec_bnk_br'";

            $p = count(DB::select($agntFileAna1));
            //dd($p);

            if ($p > 0) {
                $bnkRoutingData = DB::select($agntFileAna1);

                // dd($bnkRoutingData);

                $bankCode = $bnkRoutingData[0]->routing_no[0] . $bnkRoutingData[0]->routing_no[1] . $bnkRoutingData[0]->routing_no[2];

                $foundMatchingDist = $bnkRoutingData[0]->routing_no[3] . $bnkRoutingData[0]->routing_no[4];
                $routing = $bnkRoutingData[0]->routing_no;

                $distQ = "select * from city where bbCodeRouting='$foundMatchingDist'";
                $distSysR = DB::select($distQ);

                $distNameSys = $distSysR[0]->district_city;
                $foundMatchingDistCode = $distSysR[0]->code;

                $branch_q = "select * from bnk_br_info br left join bnk_info bnk on br.bankCode=bnk.bnkKeyCode where br.routingNumber='$routing'";
                $bnkR = DB::select($branch_q);

                $foundMatchingBankCode = $bnkR[0]->bnkKeyCode;
                $matchBankNameStr = $bnkR[0]->bank;

                $foundMatchingBankBrCode = $bnkR[0]->brKeyCode;
                $matchBranchNameStr = strtoupper($bnkR[0]->branchName);
                $bnkBrRoutingNumber = $bnkR[0]->routingNumber;

                $history_match = 1;
            }
        }

        // part 1, bank check by analytics.................................................................................................

        if (($routingMatch == 0 or $routingMatch == '') and $history_match == 0) {

             // dd("dlkfj");

            $ag_code = $AGENT_CODE;
            $rec_bnk = $RECEIVER_BANK;
            $rec_bnk_br = $RECEIVER_BANK_BRANCH;
            $rec_sub_2 = $RECEIVER_SUB_COUNTRY_LEVEL_2;

            // dd($ag_code);


            $agntFileAna1 = "select * from agent_file_analysis where agentCode='$ag_code' and bankName='$rec_bnk' and brName='$rec_bnk_br' and distName='$rec_sub_2'";
            $agntFileAna2 = "select * from agent_file_analysis where agentCode='$ag_code' and bankName='$rec_bnk' and distName='$rec_sub_2'";
            $agntFileAna3 = "select * from agent_file_analysis where agentCode='$ag_code' and bankName='$rec_bnk'";

            $count = count(DB::select($agntFileAna1));

            // dd($count);

            if ($count > 0) {

                $agntFileAna1R = DB::select($agntFileAna1);
                $foundMatchingBankCode = $agntFileAna1R['actBank'];
                $foundMatchingDistCode = $agntFileAna1R['actDist'];
                $foundMatchingBankBrCode = $agntFileAna1R['actBr'];

                $bankQ = "select * from bnk_br_info br left join bnk_info b on b.bnkKeyCode=br.bankCode where br.brKeyCode='$foundMatchingBankBrCode'";
                $bnkR = DB::select($bankQ);

                $foundMatchingBankCode = $bnkR['bnkKeyCode'];
                $matchBankNameStr = $bnkR['bank'];

                $matchBranchNameStr = strtoupper($bnkR['branchName']);
                //$foundMatchingBankBrCode=$bnkR['brKeyCode'];

                //$bnkBrDistSysCode=$bnkR['routingNumber'][3].$bnkR['routingNumber'][4];

                $distQ = "select * from city where bbCodeRouting='$foundMatchingDistCode'";
                $distSysR = DB::select($distQ);
                $distNameSys = $distSysR['district_city'];
                //$foundMatchingDistCode=$distSysR['code'];

                $bnkBrRoutingNumber = $bnkR['routingNumber'];
                $routingMatch = $bnkR['routingNumber'];

                dd($routingMatch);

                $analyticsMatch = 1;
            }


           // dd("dlk");
        }
        //end file analysis................................................................

        //part 2 of bank checking..............................................................
        $xyz = "Not In";


        // if not matched by analysis......................................................
        if ($matchBankNameStr == "" or empty($matchBankNameStr)) {

            // dd("lsdkfj");
            //  dd($matchBankNameStr);

            if ($trnTp == 'C')
                $RECEIVER_BANK = 'Dhaka Bank Limited';

            //$RECEIVER_BANK=strtoupper($r['RECEIVER_BANK']);
            $RECEIVER_BANK = strtoupper($RECEIVER_BANK);

            // dd($RECEIVER_BANK);

            //new code
            if ($AGENT_CODE == '235') {
                //dd("ff");

                $recBank = str_replace(' ', '', $RECEIVER_BANK);
                if ($recBank != '') {
                    $RECEIVER_BANK = strtoupper($RECEIVER_BANK);
                    // dd($RECEIVER_BANK);
                } else {
                    $RECEIVER_BANK = 'AB BANK LIMITED';
                    // dd($RECEIVER_BANK);
                }
            }




            if (empty($matchBankNameStr)) {

                // dd($matchBankNameStr);

                // dd($RECEIVER_BANK);


                $xyz = "In";
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
                $cut1 = substr($RECEIVER_BANK, 0, 3);
                $cut2 = substr($RECEIVER_BANK, 0, 10);


                //dd($RECEIVER_BANK);

                if ($cut1 == "NCC")
                    $RECEIVER_BANK = "NCC";
                if ($cut2 == "NATIONALCR")
                    $RECEIVER_BANK = "NCC";


                //dd($RECEIVER_BANK);

                $q2 = "select * from bank_match where possible_bank='$RECEIVER_BANK'";

                $d1 = DB::select($q2);

                // dd($d1);

                $c = count(DB::select($q2));

                //dd($c);




                //  .................                .....    soomething not understand ..............................................


                if ($c > 0) {

                    // dd("df");

                    $color = "GREEN";
                    $matchBankNameStr = $d1[0]->bank;
                    $foundMatchingBankCode = $d1[0]->bnkKeyCode;

                    //dd($matchBankNameStr);
                    //dd($foundMatchingBankCode);

                } else {
                    $matchBankNameStr = $RECEIVER_BANK;
                    $foundMatchingBankCode = "";
                }
            }


            //dd("f");


        }    // if matched by analysis ends......................................................



        // dd($foundMatchingBankCode);

      //  dd($analyticsMatch);
       // dd($routingMatch);
       // dd($routingMatch);


        // Checking Bank Branch Name.............................................................................................
        if ($foundMatchingBankCode > 0 and ($routingMatch == 0 and $routingMatch == '') and $analyticsMatch == 0 and $history_match == 0) {

            //dd($foundMatchingBankCode);

            //  dd("f");

            if ($trnTp == 'C') {
                $agntBr = $_SESSION['mt_agent_br_id'];
                $cashBr = "select * from  bnk_br_info where brKeyCode IN (select bnk_br_id from agent_branch_info where agent_br_key='$agntBr')";
                $cashBrR = DB::select($cashBr);
                $recBnkBrMatch = $cashBrR['branchName'];
            }

            // part 1 for branch checking................by analytics,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
            // if part 1 not found, part 2 of Checking Branch.............................................................................................
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

            $bnkBrList = DB::select("select * from  bnk_br_info where bankCode='$foundMatchingBankCode'");

            while ($rBnkBrList = $bnkBrList) {

                $AvailableBankBranch = strtoupper($rBnkBrList['branchName']);
                $AvailableBankBranch = preg_replace('/ BRANCH.*/', '', $AvailableBankBranch);

                similar_text($REC_BNK_BRANCH_NAME, $AvailableBankBranch, $percentBnkBr);

                array_push($bnkBrMatchPercent, $percentBnkBr);
                array_push($bnkBrMatch, $rBnkBrList['branchName']);
                array_push($bnkBrCodeMatch, $rBnkBrList['brKeyCode']);
                array_push($bnkBrRouting, $rBnkBrList['routingNumber']);
            }
            if (max($bnkBrMatchPercent) >= 90) {

                $bankBrMatchPercentIndexKey = array_search(max($bnkBrMatchPercent), $bnkBrMatchPercent);
                $bnkBrRoutingNumber = $bnkBrRouting[$bankBrMatchPercentIndexKey];

                $q_br = "select * from bnk_br_info where routingNumber='$bnkBrRoutingNumber'";
                $d_br = DB::select($q_br);

                $matchBranchNameStr = strtoupper($d_br['branchName']);
                $foundMatchingBankBrCode = $d_br['brKeyCode'];



                if (isset($d_br['routingNumber'])) {
                    $bnkBrDistSysCode = $d_br['routingNumber'][3] . $d_br['routingNumber'][4];

                    $distSysQ =  "select * from city where bbCodeRouting='$bnkBrDistSysCode'";
                    $distSysR = DB::select($distSysQ);
                    $distNameSys = $distSysR['district_city'];
                    $foundMatchingDistCode = $distSysR['code'];
                }
            } else {
                $matchBranchNameStr = "";
                $foundMatchingBankBrCode = 0;
                $foundMatchingDistCode = 0;
            }
        }

        $c = 0;

       // dd("dfsfd");


        if ($c == 1) {

            //  dd($c);

            $q_br = "select * from bnk_br_info where routingNumber='$bnkBrRoutingNumber'";
            $d_br = DB::select($q_br);

            $matchBranchNameStr = strtoupper($d_br['branchName']);
            $foundMatchingBankBrCode = $d_br['brKeyCode'];

            if (isset($d_br['routingNumber'])) {
                $bnkBrDistSysCode = $d_br['routingNumber'][3] . $d_br['routingNumber'][4];

                $distSysQ = "select * from city where bbCodeRouting='$bnkBrDistSysCode'";
                $distSysR = DB::select($distSysQ);
                $distNameSys = $distSysR['district_city'];
                $foundMatchingDistCode = $distSysR['code'];
            }
        }



       // dd($foundMatchingBankBrCode);
       // dd($bnkBrRoutingNumber);
        dd($routingMatch);

        //  dd("dfsfd");

        // Checking Multiple Branch...........................................................
        if ($foundMatchingBankBrCode && $bnkBrRoutingNumber and $routingMatch == 0 and $analyticsMatch == 0 and $history_match == 0) {


            // dd("dfsfd");

            $qchkBr =  "select * from bnk_br_info where bankCode='$foundMatchingBankCode' and branchName='$matchBranchNameStr'";
            if (DB::select($qchkBr) > 1) {
                $c12 = 0;

                while ($br_q = DB::select($qchkBr)) {
                    $routing12 = $br_q['routingNumber'];
                    $brKeycd12 = $br_q['brKeyCode'];
                    $brN12 = $br_q['branchName'];
                    $a12 = str_split($routing12);
                    $dis_code12 = $a12[3] . $a12[4];
                    $q_dis12 = "select * from district where bb_code='$dis_code12'";
                    $dis_d12 = DB::select($q_dis12);
                    $dis_name12 = $dis_d12['name'];
                    if (strtoupper($dis_name12) == strtoupper($RECEIVER_SUB_COUNTRY_LEVEL_2)) {
                        $c12 = 1;
                        $r12 = $routing12;
                        $disCd12 = $dis_code12;
                        break;
                    }
                }

                if ($c12 == 1) {
                    $foundMatchingBankBrCode = $brKeycd12;
                    //  $bnkBrRoutingNumber = $r12;
                    $matchBranchNameStr = $brN12;
                    // $foundMatchingDist = $disCd12;
                } else
                    $foundMatchingBankBrCode = 0;
            }
        }




        // dd("dfsfd");


        // final declaration.......................................
        if (!$foundMatchingBankBrCode || !$bnkBrRoutingNumber) {
            $color = "RED";
            $tdCol = "RED";
            $foundMatchingBankBrCode = 0;
            $bnkBrRoutingNumber = 0;

            // dd($foundMatchingBankBrCode);
        }


        //bank asia A/c number check for H.O routing number...............................................................
        $ba_acc = $RECEIVER_ACCOUNT_NUMBER;
        $x1 = str_split($ba_acc);
        $ba_acc_start = $x1[0] . $x1[1] . $x1[2];

        return [

            "foundMatchingBankBrCode" => $foundMatchingBankBrCode,
            "bnkBrRoutingNumber" => $bnkBrRoutingNumber

        ];
    }
}
