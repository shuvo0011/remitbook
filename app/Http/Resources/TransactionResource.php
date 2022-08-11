<?php

namespace App\Http\Resources;

use App\Http\Traits\TransactionAccepted\BankDepositTransactionDestinationFinding;
use App\Http\Traits\TransactionAccepted\CashTransactionDestinationFinding;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class TransactionResource extends JsonResource
{
    use CashTransactionDestinationFinding, BankDepositTransactionDestinationFinding;
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
            "transaction_destination" => $this->findTransactionDestination($this->RECEIVER_BANK, $this->RECEIVER_BANK_BRANCH, $this->RECEIVER_SUB_COUNTRY_LEVEL_2, $this->RECIEVER_BANK_BR_ROUTING_NUMBER, $this->AGENT_CODE, $this->trnTp)
        ];
    }



    public function findTransactionDestination($RECEIVER_BANK, $RECEIVER_BANK_BRANCH, $RECEIVER_SUB_COUNTRY_LEVEL_2, $RECIEVER_BANK_BR_ROUTING_NUMBER, $AGENT_CODE, $trnTp)
    {


        if ($trnTp == 'C') { // cash transaction 
            return $this->findOutCashTransactionDestination($RECEIVER_BANK, $RECEIVER_BANK_BRANCH, $RECEIVER_SUB_COUNTRY_LEVEL_2, $RECIEVER_BANK_BR_ROUTING_NUMBER, $AGENT_CODE, $trnTp);
        } else { // account-credit transaction

           // dd($RECEIVER_BANK);
            return $this->findOutBankDepositTransactionDestination($RECEIVER_BANK, $RECEIVER_BANK_BRANCH, $RECEIVER_SUB_COUNTRY_LEVEL_2, $RECIEVER_BANK_BR_ROUTING_NUMBER, $AGENT_CODE, $trnTp);
            
       
        }
    }
}
