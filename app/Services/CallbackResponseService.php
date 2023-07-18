<?php

namespace App\Services;

class CallbackResponseService{
    public static function getQpStatusCode( $data ){
        $lastStatusCode = null;
        foreach ($data as $operation) {
            if ($operation['qp_status_code'] == 'Approved') {
                return $operation['qp_status_code']; // Return the 'Approved' status code immediately.
            }
            $lastStatusCode = $operation['qp_status_code'];
        }
        return $lastStatusCode; // Return the 'qp_status_code' of the last element when 'Approved' status code not found.
    }
}