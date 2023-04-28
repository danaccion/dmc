<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use QuickPay\QuickPay;

class QuickPayController extends Controller
{

    public function getAllPaymentByOrderId(Request $request)
    {
        // Get all payments By Order ID
        try {
            $api_key = 'c131a294f2a44ff648ade3941195fcda6a83c2b579e788ac16327b8701735c1b';
            $client = new QuickPay(":{$api_key}");
            // Create payment
            $payments = $client->request->get('/payments?order_id='.$request->id);
                $status = $payments->httpStatus();
                if ($status == 200) {
                    $jsonObj = json_encode($payments);
                    $responseData = json_decode($jsonObj);
                    $responseData = $responseData->response_data;
                    $array = $responseData;
                    print_r($array);
                }
            } catch (Exception $e) {
                echo $e;
            }
    }


    public function getAllPayment()
    {
        // Get all payments
        try {
            $api_key = 'c131a294f2a44ff648ade3941195fcda6a83c2b579e788ac16327b8701735c1b';
            $client = new QuickPay(":{$api_key}");
            // Create payment
            $payments = $client->request->get('/payments');
                $status = $payments->httpStatus();
                if ($status == 200) {
                    $jsonObj = json_encode($payments);
                    $responseData = json_decode($jsonObj);
                    $responseData = $responseData->response_data;
                    $array = $responseData;
                    print_r($array);
                }
            } catch (Exception $e) {
                echo $e;
            }
    }

    public function pay(){
       
        try {
            $api_key = 'c131a294f2a44ff648ade3941195fcda6a83c2b579e788ac16327b8701735c1b';
            $client = new QuickPay(":{$api_key}");
            // Create payment
            $order_id='';
            $random_number = mt_rand(100000, 999999);
            $order_id .= $random_number;
            $facilitator = 'creditcard';
            $amount = 500;
            $currency = 'DKK';
            $testmode = true;
            $autocapture = true;
            
            $initform = array(
                'order_id' => $order_id,
                'currency' => $currency,
            );
                $payments = $client->request->post('/payments', $initform);
                $status = $payments->httpStatus();
                if ($status == 201) {
                    $jsonObj = json_encode($payments);
                    $responseData = json_decode($jsonObj);
                    $responseData = $responseData->response_data;
                    $array = $responseData;
                    $jsonData = json_decode($array);
                    $id = $jsonData->id;
                    // Authorized
                    $this->putPayment($id, $client);
                }
            } catch (Exception $e) {
                echo $e;
            }  
    }


    public function putPayment($id, $client)
    {
         $form = array(
            'id'=>$id,
            'amount' => 1,
            "merchant_id" => 171565,
            "payment_methods" => "creditcard",
            "auto_capture" => true,
            "continue_url" => route('success'),
            "cancel_url" =>  route('cancel'),
            "callback_url" => route('callback')
         );
        $url = '/payments/'.$id.'/link';
        $payments =  $client->request->put($url, $form);
        $status = $payments->httpStatus();
        if($status == 200){
            $jsonObj = json_encode($payments);
            $responseData = json_decode($jsonObj);
            $responseData = $responseData->response_data;
            $array = $responseData;
            $jsonData = json_decode($array);
            $link = $jsonData->url;
            echo $link;
            header('Location:'.$link);
            exit();
        }
    }

    public function getSuccess(Request $request)
    {
        return view('pensopay.success');
    }

    public function getCancel(Request $request)
    {
        return view('pensopay.cancel');
    }

    public function getCallback(Request $request)
    {
        return view('pensopay.callback');
    }
}
