<?php
namespace App\Http\Traits\TransactionAccepted;

use App\Http\Traits\OwnBankInfo;

trait CashTransactionDestinationFinding {

    use OwnBankInfo;

    public function findOutCashTransactionDestination($RECEIVER_BANK, $RECEIVER_BANK_BRANCH, $RECEIVER_SUB_COUNTRY_LEVEL_2, $RECIEVER_BANK_BR_ROUTING_NUMBER, $AGENT_CODE, $trnTp) {
        $bank_code = $this->ownBankCode();

        //  try to routing no match
        if($RECIEVER_BANK_BR_ROUTING_NUMBER != ''){
            // findout own bank branch code

        }

        // try to branch name match


        // try to log data match 




        
        return [
            "destination_found" => true,
            "bank_code" => $this->ownBankCode()
        ];
    }





}