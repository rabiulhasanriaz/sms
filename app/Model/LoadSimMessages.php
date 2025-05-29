<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadSimMessages extends Model
{
    protected $fillable = [
        'user_id', 'sim_no', 'operator_company', 'message', 'sender', 'serial_id', 'status'
    ];
    protected $dates = [
        'created_at',
    ];

    public function user_name(){
        return $this->belongsTo('App\Model\User','user_id','id');
    }

    public static function getTransactionIdFromMessage($total_message, $opcompany = null)
    {
        try {
            if (strpos($total_message, 'transaction ID ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('transaction ID ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            }elseif (strpos($total_message, 'Transaction ID is ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction ID is ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            }
            elseif (strpos($total_message, 'Transaction ID ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction ID ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            } elseif (strpos($total_message, 'Transaction number is ') !== false) {
                /*robi/airtel format*/
                $message_with_trx_id = explode('Transaction number is ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];

                if (substr($trx_id, -4) == 'Your') {
                    $trx_id = substr($trx_id, 0, -4);
                }
            } elseif (strpos($total_message, 'Transaction number ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction number ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            }elseif (strpos($total_message, 'Transaction ID is ') !== false) {
                /*teletalk format*/
                $message_with_trx_id = explode('Transaction ID is ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];

                if (substr($trx_id, -4) == 'Your') {
                    $trx_id = substr($trx_id, 0, -4);
                }
            } else {
                $trx_id = "";
            }
        } catch (\Exception $e) {
            $trx_id = "";
        }

        return $trx_id;
    }
}
